<?php

namespace Cirote\Movimientos\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cirote\Activos\Models\Activo;
use Cirote\Activos\Models\Broker;
use Cirote\Movimientos\Actions\CalcularValorActualDeLasPosicionesAbiertasAction as valorActual;
use Cirote\Movimientos\Models\Posicion;

class PosicionesController extends Controller
{
	public function index()
    {
    	dd(Posicion::all());
    }

    public function resumenAbiertas()
    {
        return view('movimientos::posiciones.resumenAbiertas')
            ->withInversionRealizada(Posicion::abiertas()->selectRaw('sum(monto_en_dolares) as total_en_dolares')->first()->total_en_dolares)
            ->withPosiciones(Posicion::with(['activo.tickers', 'activo.precio', 'broker'])->abiertas()->resumir()->orderByDesc('monto_total_en_dolares')->paginate(10));
    }

	public function abiertas(Activo $activo = null, Broker $broker = null)
    {
        $posiciones = Posicion::with(['activo.tickers', 'broker'])->abiertas()->byApertura();

        if ($activo)
            $posiciones->byActivo($activo);

        if ($broker)
            $posiciones->byBroker($broker);

        return view('movimientos::posiciones.abiertas')
        	->withPosiciones($posiciones->paginate(10));
    }

    public function resumenCerradas()
    {
        return view('movimientos::posiciones.resumenCerradas')
            ->withPosiciones(Posicion::with(['activo.tickers', 'broker'])->cerradas()->resumir()->orderByDesc('monto_total_en_dolares')->paginate(10));
    }

	public function cerradas(Activo $activo = null, Broker $broker = null)
    {
        $posiciones = Posicion::with(['activo.tickers', 'broker'])->cerradas()->byCierre();

        if ($activo)
            $posiciones->byActivo($activo);

        if ($broker)
            $posiciones->byBroker($broker);

        return view('movimientos::posiciones.cerradas')
            ->withPosiciones($posiciones->paginate(10));
    }

	public function prueba()
    {
        $iol = new Iol();

        $iol->execute();
    }
}
