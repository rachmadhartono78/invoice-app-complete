<?php

namespace App\Services;

class TemplateNotification
{
    public static function getTemplateWhatsapp($templateKey, array $data = [])
    {
        return self::parseTemplate($templateKey, $data);
    }

    public static function getTemplateApp($templateKey, array $data = [])
    {
        return self::parseTemplate($templateKey, $data, true);
    }

    protected static function parseTemplate($key, array $data, $removeNewlines = false)
    {
        $template = config("notification_templates.$key", '');

        if (! empty($data)) {
            $placeholders = array_map(fn ($key) => '{{'.$key.'}}', array_keys($data));
            if ($removeNewlines && isset($data['link'])) {
                $data['link'] = '<a href="'.$data['link'].'">'.$data['link'].'</a>';
            }
            $template = str_replace($placeholders, array_values($data), $template);
        }

        return $removeNewlines ? str_replace(["\n", "\r"], '', $template) : $template;
    }
}
