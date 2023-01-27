<?php

namespace dyutin\FileManager\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Exception;

class TerminalController extends Controller
{

    /**
     * @codeCoverageIgnore
     * @param Request $request
     * @return string
     */
    public function run(Request $request)
    {
        $command = $request->post('command', '');

        try {
            if ($artisan = $this->ifArtisanCommandGetCommand($command)) {
                Artisan::call($artisan);

                return Artisan::output();
            }

            $process = new Process(explode(' ', $command));
            $process->run();
            return $process->getOutput();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * @codeCoverageIgnore
     * @param string $command
     * @return string|bool
     */
    protected function ifArtisanCommandGetCommand($command)
    {
        if (preg_match('/^php\s+artisan.+?(.*)/', $command, $find)) {
            if (isset($find[1])) {
                $command = $find[1];
            }
        }

        return Arr::has(Artisan::all(), $command) ? $command : false;
    }
}
