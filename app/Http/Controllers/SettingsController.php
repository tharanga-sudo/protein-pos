<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettings;
use App\Models\Setting;

/**
 * Class SettingsController
 *
 * @package App\Http
 */
class SettingsController extends AuthenticatedController
{
    public function index()
    {
        return view('settings.index', [
            'creditCardTax' => Setting::getValueByKey(Setting::KEY_CREDIT_CARD_TAX, 2)
        ]);
    }

    public function update(UpdateSettings $request)
    {
        $creditCardTax = Setting::firstOrCreate(['key' => Setting::KEY_CREDIT_CARD_TAX]);
        $creditCardTax->value = $request->get('credit_card_tax');
        $creditCardTax->saveOrFail();

        return redirect()->route('settings.index')->with('flashes.success', 'Settings updated');
    }
}