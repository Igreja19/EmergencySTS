<?php
namespace common\components;

class UiHelper
{
    public static function badge(string $label, string $type = 'secondary'): string
    {
        return '<span class="badge bg-' . $type . ' rounded-pill px-3 py-2">' . htmlspecialchars($label) . '</span>';
    }

    public static function prioridadeBadge(?string $prioridade): string
    {
        $map = [
            'Vermelho' => 'danger',
            'Laranja'  => 'warning',
            'Amarelo'  => 'warning',
            'Verde'    => 'success',
            'Azul'     => 'info',
        ];
        if (!$prioridade) return self::badge('—', 'secondary');
        return self::badge($prioridade, $map[$prioridade] ?? 'secondary');
    }

    public static function estadoConsultaBadge(?string $estado): string
    {
        $map = ['Aberta'=>'primary','Em curso'=>'info','Encerrada'=>'success'];
        if (!$estado) return self::badge('—', 'secondary');
        return self::badge($estado, $map[$estado] ?? 'secondary');
    }

    public static function pulseiraStatusBadge(?string $status): string
    {
        $map = ['Aguardando'=>'secondary','Em atendimento'=>'info','Atendido'=>'success'];
        if (!$status) return self::badge('—', 'secondary');
        return self::badge($status, $map[$status] ?? 'secondary');
    }

    public static function tipoNotificacaoBadge(?string $tipo): string
    {
        $map = ['Consulta'=>'primary','Prioridade'=>'warning','Geral'=>'dark'];
        if (!$tipo) return self::badge('—', 'secondary');
        return self::badge($tipo, $map[$tipo] ?? 'secondary');
    }

    public static function lidaBadge(int $lida): string
    {
        return $lida ? self::badge('Lida', 'success') : self::badge('Por ler', 'secondary');
    }
}
