<?php

namespace App\Http\Controllers;

use App\Models\Thermostat;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FulfillmentController extends Controller
{
    public function __invoke(Request $request)
    {
        $response = null;

        // Extract request type
        switch ($request->input('inputs.0.intent')) {
            case 'action.devices.QUERY':
                $response = $this->queryResponse();
                break;
            case 'action.devices.SYNC':
                $response = $this->syncRequest();
                break;
            case 'action.devices.EXECUTE':
                $response = $this->syncExecute($this->syncExecute($request->input('inputs.0.payload.commands'))); // Extract list of commands
                break;
        }

        return $response;
    }

    private function queryResponse()
    {
        $devices = [];

        // Extract our devices states
        foreach (Thermostat::all() as $thermostat) {
            $devices[$thermostat->id] = [
                'status' => 'SUCCESS',
                'online' => $thermostat->online,
                'thermostatMode' => $thermostat->mode,
                'thermostatTemperatureSetpoint' => $thermostat->expected_temperature,
                'thermostatTemperatureAmbient' => $thermostat->current_temperature,
                'thermostatHumidityAmbient' => $thermostat->humidity,
            ];
        }

        return response([
            'requestId' => "6894439706274654514",
            'payload' => [
                "agentUserId" => "user123",
                'devices' => $devices,
            ],
        ]);
    }

    private function syncRequest()
    {
        $devices = [];

        // Define our devices
        foreach (Thermostat::all() as $thermostat) {
            $devices[] = [
                'id' => $thermostat->id,
                'type' => "action.devices.types.THERMOSTAT",
                'traits' => [
                    "action.devices.traits.TemperatureSetting"
                ],
                'name' => [
                    'name' => 'Thermostat'
                ],
                'willReportState' => true,
                'attributes' => [
                    'availableThermostatModes' => [
                        'off',
                        'heat',
                        'cool',
                    ],
                    'thermostatTemperatureRange' => [
                        'minThresholdCelsius' => 18,
                        'maxThresholdCelsius' => 30,
                    ],
                    'thermostatTemperatureUnit' => 'C'
                ],
                'deviceInfo' => [
                    'manufacturer' => 'smart-home-inc',
                ],
            ];
        }

        return response([
            'requestId' => "6894439706274654512",
            'payload' => [
                "agentUserId" => "user123",
                'devices' => $devices,
            ],
        ]);
    }

    private function syncExecute(array $commands)
    {
        foreach ($commands as $command) {
            // Get devices for execute command
            $thermostats = Thermostat::whereIn('id', Arr::pluck($command['devices'], 'id'))->get();

            foreach ($command['execution'] as $executionItem) {
                switch ($executionItem['command']) {
                    // Handle set point command and save it in our model
                    case 'action.devices.commands.ThermostatTemperatureSetpoint':
                        foreach ($thermostats as $thermostat) {
                            $thermostat->update([
                                'expected_temperature' => $executionItem['params']['thermostatTemperatureSetpoint'],
                            ]);
                        }
                        break;
                    // Handle set set mode command and save it in our model
                    case 'action.devices.commands.ThermostatSetMode':
                        foreach ($thermostats as $thermostat) {
                            $thermostat->update([
                                'mode' => $executionItem['params']['thermostatMode'],
                            ]);
                        }
                        break;
                }
            }
        }

        // It not necessary to return data for command request
        return response([]);
    }
}
