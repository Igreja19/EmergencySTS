<?php
namespace common\helpers;

class UserAgentHelper
{
    public static function format($ua)
    {
        $browser = 'Desconhecido';
        $os = 'Desconhecido';

        // BROWSER
        if (stripos($ua, 'Chrome') !== false && stripos($ua, 'Edg') === false) $browser = 'Chrome';
        if (stripos($ua, 'Edg') !== false) $browser = 'Edge';
        if (stripos($ua, 'Firefox') !== false) $browser = 'Firefox';
        if (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) $browser = 'Safari';
        if (stripos($ua, 'OPR') !== false || stripos($ua, 'Opera') !== false) $browser = 'Opera';

        // SISTEMA
        if (stripos($ua, 'Windows') !== false) $os = 'Windows';
        if (stripos($ua, 'Android') !== false) $os = 'Android';
        if (stripos($ua, 'iPhone') !== false) $os = 'iPhone';
        if (stripos($ua, 'iPad') !== false) $os = 'iPad';
        if (stripos($ua, 'Mac OS') !== false || stripos($ua, 'Macintosh') !== false) $os = 'macOS';
        if (stripos($ua, 'Linux') !== false && $os === 'Desconhecido') $os = 'Linux';

        return "{$browser} · {$os}";
    }
}
