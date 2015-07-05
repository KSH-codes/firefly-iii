<?php

namespace FireflyIII\Helpers\Csv\Converter;

use Auth;
use FireflyIII\Models\Account;
use FireflyIII\Models\AccountType;

/**
 * Class AssetAccountIban
 *
 * @package FireflyIII\Helpers\Csv\Converter
 */
class AssetAccountIban extends BasicConverter implements ConverterInterface
{

    /**
     * @return Account|null
     */
    public function convert()
    {
        // is mapped? Then it's easy!
        if (isset($this->mapped[$this->index][$this->value])) {
            $account = Auth::user()->accounts()->find($this->mapped[$this->index][$this->value]);

            return $account;
        }
        // find or create new account:
        $accountType = AccountType::where('type', 'Asset account')->first();
        $set         = Auth::user()->accounts()->where('account_type_id', $accountType->id)->get();
        /** @var Account $entry */
        foreach ($set as $entry) {
            if ($entry->iban == $this->value) {
                return $entry;
            }
        }

        // create it if doesnt exist.
        $account = Account::firstOrCreateEncrypted(
            [
                'name'            => $this->value,
                'iban'            => $this->value,
                'user_id'         => Auth::user()->id,
                'account_type_id' => $accountType->id,
                'active'          => 1,
            ]
        );

        return $account;
    }
}