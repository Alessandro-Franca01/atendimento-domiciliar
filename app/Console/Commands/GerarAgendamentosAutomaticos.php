<?php

namespace App\Console\Commands;

use App\Services\AgendamentoService;
use Illuminate\Console\Command;

class GerarAgendamentosAutomaticos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agendamentos:gerar-automaticos {--dias=30 : Número de dias para gerar agendamentos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera agendamentos automáticos baseados nos horários fixos das sessões';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando geração de agendamentos automáticos...');
        
        $dias = $this->option('dias');
        $this->info("Gerando agendamentos para os próximos {$dias} dias...");
        
        $agendamentoService = new AgendamentoService();
        $agendamentosCriados = $agendamentoService->gerarAgendamentosAutomaticos($dias);
        
        $this->info("Agendamentos criados com sucesso: {$agendamentosCriados}");
        $this->info('Processo concluído!');
        
        return Command::SUCCESS;
    }
}
