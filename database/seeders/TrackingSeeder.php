<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tracking\SessionBlock;
use App\Models\Tracking\Event;
use App\Models\Tracking\DailyMetric;
use App\Models\Tracking\Lead;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrackingSeeder extends Seeder
{
    public function run(): void
    {
        $pages = ['/home', '/precos', '/sobre', '/contacto', '/vagas/dev-backend'];
        $eventTypes = ['page_view', 'heartbeat', 'form_start', 'restricted_click'];
        
        // Vamos gerar dados para os últimos 7 dias
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Simular 20 sessões por dia
            for ($s = 0; $s < 20; $s++) {
                // Criamos o bloco primeiro para pegar o ID dele
                $startTime = $date->copy()->addHours(rand(8, 22))->addMinutes(rand(0, 59));
                $isConverted = (rand(1, 10) > 8); 

                $block = SessionBlock::create([
                    'id' => (string) Str::uuid(),
                    'session_id' => (string) Str::uuid(), // O identificador da sessão (browser/cookie)
                    'started_at' => $startTime,
                    'ended_at' => $startTime->copy()->addSeconds(rand(30, 600)),
                    'duration_seconds' => rand(30, 600),
                    'landing_page' => $pages[array_rand($pages)],
                    'exit_page' => $pages[array_rand($pages)],
                    'is_converted' => $isConverted,
                ]);

                // 2. Criar de 3 a 8 eventos vinculados a este bloco
                for ($e = 0; $e < rand(3, 8); $e++) {
                    Event::create([
                        'id' => (string) Str::uuid(),
                        'session_id' => $block->id, // Chave Estrangeira para session_blocks.id
                        'event_type' => $eventTypes[array_rand($eventTypes)],
                        'url' => $pages[array_rand($pages)],
                        'payload' => json_encode(['browser' => 'Chrome', 'device' => 'desktop']),
                        'created_at' => $startTime->copy()->addSeconds(rand(1, 200)),
                    ]);
                }

                // 3. Se converteu, cria o Lead usando o session_id do bloco
                if ($isConverted) {
                    Lead::create([
                        'id' => (string) Str::uuid(),
                        'session_id' => $block->session_id, // Link para comportamento na LP
                        'name' => 'Lead de Teste ' . Str::random(5),
                        'email' => 'lead' . Str::random(5) . '@exemplo.com',
                        'phone' => '91' . rand(1000000, 9999999),
                        'status' => 'converted',
                        'converted_at' => $startTime->copy()->addMinutes(2),
                    ]);
                }
            }

            // 4. Gerar métricas diárias consolidadas
            foreach ($pages as $page) {
                DailyMetric::create([
                    'id' => (string) Str::uuid(),
                    'date' => $date->format('Y-m-d'),
                    'page_path' => $page,
                    'total_views' => rand(100, 500),
                    'total_sessions' => rand(50, 150),
                    'total_conversions' => rand(0, 10),
                    'bounce_rate' => rand(20, 70) + (rand(0, 9) / 10),
                ]);
            }
        }
    }
}