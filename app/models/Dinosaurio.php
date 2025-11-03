<?php
/**
 * dinosaurios.php
 * Módulo de dominio para Draftosaurus (modo base, tablero verano).
 * - Especies y colores
 * - Composición de bolsa según jugadores
 * - Robar manos de 6 / pasar a la izquierda
 * - Validadores de restricciones del dado (modo base)
 */

declare(strict_types=1);

class Especie
{
    // 6 especies del modo base (incluye T-Rex como especie especial)
    public const TREX = 'TREX';
    public const STEGOSAURUS = 'STEG';
    public const TRICERATOPS = 'TRIC';
    public const PARASAURO = 'PARA';
    public const DIPLODOCUS = 'DIPL';
    public const VELOCIRAPTOR = 'VELO';

    /** Nombre legible por especie */
    public const NOMBRE = [
        self::TREX => 'T-Rex',
        self::STEGOSAURUS => 'Stegosaurus',
        self::TRICERATOPS => 'Triceratops',
        self::PARASAURO => 'Parasaurolophus',
        self::DIPLODOCUS => 'Diplodocus',
        self::VELOCIRAPTOR => 'Velociraptor',
    ];

    /**
     * Colores sugeridos (hex). Ajustalos a tu paleta si ya tenés UI definida.
     * Son solo por defecto y NO afectan reglas.
     */
    public const COLOR = [
        self::TREX => '#D9534F', // rojo
        self::STEGOSAURUS => '#5CB85C', // verde
        self::TRICERATOPS => '#5BC0DE', // celeste
        self::PARASAURO => '#F0AD4E', // naranja
        self::DIPLODOCUS => '#9B59B6', // violeta
        self::VELOCIRAPTOR => '#34495E', // gris azulado
    ];

    /** Lista de especies base (10 de cada una, total 60) */
    public static function todas(): array
    {
        return [
            self::TREX,
            self::STEGOSAURUS,
            self::TRICERATOPS,
            self::PARASAURO,
            self::DIPLODOCUS,
            self::VELOCIRAPTOR,
        ];
    }

    public static function esTRex(string $especie): bool
    {
        return $especie === self::TREX;
    }
}

class DadoColocacion
{
    // Caras del dado modo personalizado
    public const RIVER = 'RIVER';
    public const FOREST = 'FOREST';
    public const PLAINS = 'PLAINS';
    public const MOUNTAINS = 'MOUNTAINS';
    public const CAFETERIA = 'CAFETERIA';
    public const RESTROOMS = 'RESTROOMS';
    public const T_REX = 'T_REX'; // por ejemplo

    /** Etiquetas legibles para UI */
    public const NOMBRE = [
        self::RIVER => 'Zona del Río',
        self::FOREST => 'Bosque',
        self::PLAINS => 'Llanuras',
        self::MOUNTAINS => 'Montañas',
        self::CAFETERIA => 'Cafetería',
        self::RESTROOMS => 'Baños',
        self::T_REX => 'Recinto del T-Rex',
    ];
}

class DinoFactory
{
    /**
     * Arma la bolsa según cantidad de jugadores.
     * Reglas (modo base):
     * - 5 jugadores: 60 (10 de cada especie)
     * - 4 jugadores: quitar 2 de cada especie → 48 en bolsa
     * - 3 jugadores: quitar 4 de cada especie → 36 en bolsa
     * - 2 jugadores: quitar 2 de cada especie → 48 en bolsa (juego a 4 rondas)
     */
    public static function bolsaPorJugadores(int $jugadores): array
    {
        if ($jugadores < 2 || $jugadores > 5) {
            throw new InvalidArgumentException('Cantidad de jugadores inválida (solo 2 a 5).');
        }

        $porEspecieBase = 10;
        $quitarPorEspecie = match ($jugadores) {
            5 => 0,
            4 => 2,
            3 => 4,
            2 => 2,
        };

        $bolsa = [];
        foreach (Especie::todas() as $esp) {
            $cantidad = $porEspecieBase - $quitarPorEspecie;
            for ($i = 0; $i < $cantidad; $i++) {
                $bolsa[] = $esp;
            }
        }

        // Mezclar (Fisher–Yates simple)
        self::mezclar($bolsa);
        return $bolsa;
    }

    /** Roba n dinosaurios de la bolsa (y los quita de la bolsa por referencia). */
    public static function robarMano(array &$bolsa, int $cantidad = 6): array
    {
        $mano = [];
        for ($i = 0; $i < $cantidad && !empty($bolsa); $i++) {
            $mano[] = array_pop($bolsa); // ya viene mezclada
        }
        return $mano;
    }

    /**
     * Pasa manos a la izquierda:
     * hands[i] se entrega a jugador (i+1) y la última va al jugador 0.
     */
    public static function pasarALaIzquierda(array $hands): array
    {
        if (count($hands) <= 1)
            return $hands;
        $primera = array_shift($hands);
        $hands[] = $primera;
        return $hands;
    }

    /** Color por especie (para UI). */
    public static function color(string $especie): string
    {
        return Especie::COLOR[$especie] ?? '#777777';
    }

    /** Barajar en su lugar. */
    private static function mezclar(array &$arr): void
    {
        for ($i = count($arr) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
        }
    }
}

/**
 * Helpers de validación de restricciones del DADO.
 * Se basan en metadatos del recinto destino (que provee tu modelo de tablero).
 *
 * Estructura esperada de $recinto:
 * [
 *   'id' => 'bosque_semejanza',
 *   'zona' => 'WOODLANDS'|'GRASSLANDS',   // zona del tablero verano
 *   'lado' => 'LEFT'|'RIGHT',             // relativo al río (izq=Food Court, der=Restrooms)
 *   'contiene_trex' => bool,
 *   'vacio' => bool
 * ]
 */
class ValidadorDado
{
    public static function puedeColocar(string $caraDado, array $recinto, string $especie): bool
    {
        switch ($caraDado) {
            case DadoColocacion::FOREST:
                return strtoupper($recinto['zona'] ?? '') === 'FOREST';
            case DadoColocacion::PLAINS:
                return strtoupper($recinto['zona'] ?? '') === 'PLAINS';
            case DadoColocacion::MOUNTAINS:
                return strtoupper($recinto['zona'] ?? '') === 'MOUNTAINS';
            case DadoColocacion::CAFETERIA:
                return strtoupper($recinto['zona'] ?? '') === 'CAFETERIA';
            case DadoColocacion::RESTROOMS:
                return strtoupper($recinto['zona'] ?? '') === 'RESTROOMS';
            case DadoColocacion::RIVER:
                return strtoupper($recinto['zona'] ?? '') === 'RIVER';
            case DadoColocacion::T_REX:
                // ejemplo: sólo permite T-Rex si la zona es T_REX
                return strtoupper($recinto['zona'] ?? '') === 'T_REX';
            default:
                return true;
        }
    }
}


/**
 * Bonus y puntajes especiales de elementos globales (no de cada recinto):
 * - Río: cada dino = 1 punto (esto se calcula en tu lógica de puntaje)
 * - T-Rex: +1 punto al recinto si contiene al menos 1 T-Rex (máximo +1 por recinto)
 */
class BonusGlobal
{
    public static function bonusTRexPorRecinto(bool $contieneTRex): int
    {
        return $contieneTRex ? 1 : 0;
    }
}
