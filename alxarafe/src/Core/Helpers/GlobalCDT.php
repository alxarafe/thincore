<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */

namespace Alxarafe\Helpers;

use DateTime;
use DateTimeZone;

/**
 * This class provides homogenous support for timestamp with timezones
 *
 * @package Alxarafe\Helpers
 */
class GlobalCDT
{
    /**
     * Zona horaria por defecto en la que se guardan y se leen los datos en la base de datos.
     * Se opta por UTC por ser aséptico respecto a cambios de horario de verano/invierno
     * https://es.wikipedia.org/wiki/Tiempo_universal_coordinado
     */
    const DEFAULT_TIMEZONE = 'UTC';

    /**
     * Formato comprensible para W3C. Se utiliza por el W3C INPUT datetime y timestamp cuando tiene fecha y hora.
     * Es posible que también sea el que entiende JS. Se utiliza para retornar datos bajo demanda o por defecto usando
     * toW3C()
     */
    const W3CDATETIME = 'Y-m-d\TH:i:s';

    /**
     * Formato datetime para MYSQL con formato japonés.
     * Es el formato en el que se devuelve la fecha-hora al convertir a string, si no se especifica un formato.
     * La principal ventaja está en que el orden alfabético coincide con el orden cronológico, por lo que se puede
     * comparar.
     */
    const DATETIME = 'Y-m-d H:i:s';

    /**
     * Short datetime format.
     */
    const DATETIME_SHORT = 'Y-m-d H:i';

    /**
     * Date format.
     */
    const DATE = 'Y-m-d';

    /**
     * Long time format.
     */
    const TIME = 'H:i:s';

    /**
     * Short time format.
     */
    const TIME_SHORT = 'H:i';

    /**
     * Year format.
     */
    const YEAR = 'Y';

    /**
     * Timezone for save and read data on the database (default UTC)
     *
     * @var DateTimeZone
     */
    static public $mainTimeZone;

    /**
     * Timezone for the company.
     *
     * @var DateTimeZone
     */
    static public $companyTimeZone;

    /**
     * Timezone for the agent.
     *
     * @var DateTimeZone
     */
    static public $agentTimeZone;

    /**
     * Retorna un CustomDateTime si se le pasa una cadena con la fecha o una fecha en formato UNIX
     * Si no se le pasa ningún parámetro o null, retorna now.
     *
     * @param string|int|null $value
     *
     * @return CustomDateTime
     * @throws \Exception
     */
    static public function getCustomDateTime($value = null)
    {
        $value = $value == null ? 'now' : $value;
        $value = is_numeric($value) ? strtotime($value) : $value;
        return new CustomDateTime($value, CustomDateTime::DEFAULT_TIMEZONE);
    }

    /**
     * Retorna un CustomDateTime si se le pasa una cadena con la fecha o una fecha en formato UNIX
     * Si se le pasa null, retorna null.
     *
     * @param CustomDateTime|string|int|null $value
     *
     * @return CustomDateTime|null
     * @throws \Exception
     */
    static public function getNullableCustomDateTime($value)
    {
        if ($value == null) {
            return null;
        }
        if ($value instanceof CustomDateTime) {
            return $value;
        }
        $value = is_numeric($value) ? strtotime($value) : $value;
        return new CustomDateTime($value, CustomDateTime::DEFAULT_TIMEZONE);
    }

    /**
     * Ejecuta $function en $cdt aplicando $filter
     * Si $cdt es null, o no exite el método solicitado, retorna null directamente.
     *
     * @param CustomDateTime|null $cdt
     * @param string $function
     * @param null $filter
     *
     * @return string|null
     */
    static public function getCustomDateTimeStr($cdt, $function = 'toUTC', $filter = CustomDateTime::DATETIME)
    {
        if ($cdt == null || !method_exists($cdt, $function)) {
            return null;
        }
        return $cdt->{$function}($filter);
    }

    /**
     * Establece una hora dada una zona horaria.
     * Si no se especifica zona horaria, se utiliza la definida al crear el objeto.
     * Si no se especificó zona horaria al crear el objeto, se toma la de la empresa.
     *
     * @param string $datetime
     * @param string|null $timezone
     *
     * @throws \Exception
     */
    public function setDateTime($datetime = 'now', $timezone = null)
    {
        $this->timezone = ($timezone == null) ? self::$companyTimeZone : $timezone;
        $this->datetime = new DateTime($datetime, $this->timezone);
    }

    /**
     * Asigna la zona horaria del agente, siempre que no sea nulo.
     *
     * @param string|null $timezone
     */
    public function setAgentTimeZone($timezone)
    {
        if ($timezone != null) {
            self::$agentTimeZone = new DateTimeZone($timezone);
        }
    }

    /**
     * Retorna una cadena con la fecha en el huso horario UTC
     *
     * @param string $format
     */
    public function toUTC($format = self::DATETIME)
    {
        return $this->toCustom(self::$mainTimeZone->getName(), $format);
    }

    /**
     * Retorna una cadena con la fecha en la zona horaria y formato indicados
     *
     * @param string|null $timezone
     * @param string $format
     *
     * @return string
     */
    public function toCustom($timezone = null, $format = self::DATETIME)
    {
        $datetime = $this->datetime;
        $datetime->setTimezone(new DateTimeZone($timezone == null ? self::$companyTimeZone : $timezone));
        return $datetime->format($format);
    }

    /**
     * Retorna una cadena con la fecha en el huso horario especificado en la instancia formateada con W3C
     *
     * @param string $format
     */
    public function toW3C($format = self::W3CDATETIME)
    {
        return $this->toCustom($this->timezone, $format);
    }

    /**
     * Retorna una cadena con la fecha en el huso horario de la empresa
     *
     * @param string $format
     *
     * @return string
     */
    public function toCompany($format = self::DATETIME)
    {
        return $this->toCustom(self::$companyTimeZone->getName(), $format);
    }

    /**
     * Retorna una cadena con la fecha en el huso horario del agente
     *
     * @param string $format
     *
     * @return string
     */
    public function toAgent($format = self::DATETIME)
    {
        return $this->toCustom(self::$agentTimeZone->getName(), $format);
    }

    /**
     * Retorna la timezone de la empresa.
     *
     * @return DateTimeZone
     */
    public function getMainTimeZone()
    {
        return self::$mainTimeZone;
    }

    /**
     * Retorna la timezone de la empresa.
     *
     * @return DateTimeZone
     */
    public function getCompanyTimeZone()
    {
        return self::$companyTimeZone;
    }

    /**
     * Retorna la timezone del usuario
     *
     * @return DateTimeZone
     */
    public function getAgentTimeZone()
    {
        return self::$agentTimeZone;
    }

    /**
     * Retorna la timezone específico.
     *
     * @return DateTimeZone
     */
    public function getCustomTimeZone()
    {
        return $this->timezone;
    }

}
