<?php
namespace App\Services\Security\V1;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Class CaptchaService
 *
 * Serviço responsavel pela criação e validação de captchas
 * Utiliza a lib GD nativa do php.
 * Evita perssitencia de dados sensiveis.
 * @author Luan Quevedo
 */
class CaptchaService
{
    /** @var int Quantidade de caracteres gerados no captcha */
    private const CAPTCHA_LENGTH = 5;
    /**
     * Gera um desafio captcha
     * Fluxo de funcionamento
     * 1. Sorteio randomico de caracteres descritos
     * 2. Persistencia remporaria em cache vinculado a um UUID7
     * 3. Renderização via buffer para saida em Base64
     * 4. Adição de ruido na imagem
     *
     * @param int $width lagura de imagem
     * @param int $height altura de imagem
     * @return array{image: string, key: string} Retorna o UUID (key) e a imagem em Data URI (image).
     */
    public function generateCaptcha(int $width = 160, int $height = 60): array
    {

        //geração de codigo para validação do captcha
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*';
        $code  = '';
        $max   = strlen($chars) - 1;
        for ($i = 0; $i < self::CAPTCHA_LENGTH; $i++) {
            $code .= $chars[random_int(0, $max)];
        }

        $key = Str::uuid7()->toString();

        // Armazenamento com expiração de 3 minutos
        Cache::put("captcha:{$key}", $code, now()->addMinutes(3));

        //geração da imagem do captcha
        $image = ImageCreateTrueColor($width, $height);
        imageantialias($image, true);

        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);

        // geração de ruido
        for ($i = 0; $i < 10; $i++) {
            // Cores pastéis (100-200) para não ofuscar visibilidade do texto
            $lineColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
            imageline($image, 0, rand(0, $height), $width, rand(0, $height), $lineColor);
        }
        //Geraçãod e pontos de ruido.
        for ($i = 0; $i < 4080; $i++) {
            $dotColor = imagecolorallocate($image, rand(150, 220), rand(150, 220), rand(150, 220));
            imagesetpixel($image, rand(0, $width), rand(0, $height), $dotColor);
        }

        $length         = strlen($code);
        $characterWidth = ($width - 20) / $length;

        for ($i = 0; $i < $length; $i++) {

            $charColor = imagecolorallocate($image, rand(0, 80), rand(0, 80), rand(0, 80));
            $x         = 10 + ($i * $characterWidth) + rand(-2, 2);
            $y         = rand(15, $height - 25);
            // Utiliza fonte interna 4 da GD
            imagestring($image, 4, $x, $y, $code[$i], $charColor);
        }
        // Captura do buffer de saída para conversão Base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return [
            'key'   => $key,
            'image' => 'data:image/png;base64,' . base64_encode($imageData),
        ];
    }

    /**
     * Valida o código inserido pelo usuário contra o valor em cache.
     * * Implementa o conceito de 'Atomic Pull': uma vez consultado, o código é
     * removido do cache.
     *
     * @param string $key UUID de identificação do captcha.
     * @param string $input Valor digitado pelo usuário.
     * @return bool True se o código for válido e estiver dentro do prazo; False caso contrário.
     */
    public function validateCaptcha(string $key, string $input): bool
    {
        // Cache::pull garante o uso único (Single Use Token)
        $storedCode = Cache::pull("captcha:{$key}");

        if (! $storedCode) {
            return false;
        }
        // Comparação Type-Safe e Case-Sensitive
        return $input === $storedCode;
    }
}
