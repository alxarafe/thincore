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
class CustomDateTime
{
    /**
     * Object timezone. Es la timezone establecida por defecto para la instancia del objeto
     *
     * @var DateTimeZone
     */
    private $timezone;

    /**
     * Es el objeto que contiene la fecha y hora en formato UTC. No se modifica salvo que se solicite
     * cambiar la fecha/hora, pero siempre se guardará con el formato por defecto (DEFAULT_TIMEZONE)
     *
     * @var DateTime
     */
    private $datetime;

    /**
     * Establece las zonas horarias por defecto para almacenar los datos y para mostrar según definición
     * de los datos de la empresa (get_default_timezone())
     * Recibe como parámetro una fecha en formato UTC, o para la zona horaria especificada
     * Si no se especifica timezone, se asigna la zona horaria de la empresa.
     *
     * @param string $datetime Datetime a utilizar, o por defecto now
     * @param string|null $timezone Por defecto se aplica el de la empresa, para datos de la DB debe ser self::DEFAULT_TIMEZONE.
     *
     * @throws \Exception
     */
    public function __construct($datetime = 'now', $timezone = null)
    {
        if (GlobalCDT::$mainTimeZone == null) {
            GlobalCDT::$mainTimeZone = new DateTimeZone(GlobalCDT::DEFAULT_TIMEZONE);
            GlobalCDT::$companyTimeZone = new DateTimeZone(get_default_timezone());
            GlobalCDT::$agentTimeZone = GlobalCDT::$companyTimeZone;
        }

        $this->timezone = ($timezone == null) ? GlobalCDT::$companyTimeZone : new DateTimeZone($timezone);
        $this->setDateTime($datetime, $this->timezone);
    }

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
        return new CustomDateTime($value, GlobalCDT::DEFAULT_TIMEZONE);
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
        return new CustomDateTime($value, GlobalCDT::DEFAULT_TIMEZONE);
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
    static public function getCustomDateTimeStr($cdt, $function = 'toUTC', $filter = GlobalCDT::DATETIME)
    {
        if ($cdt == null || !method_exists($cdt, $function)) {
            return null;
        }
        return $cdt->{$function}($filter);
    }

}
