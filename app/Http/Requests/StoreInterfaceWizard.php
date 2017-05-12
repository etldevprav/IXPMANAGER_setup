<?php

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace IXP\Http\Requests;

use Auth;

use Entities\{
    PhysicalInterface as PhysicalInterfaceEntity
};

use Illuminate\Foundation\Http\FormRequest;


class StoreInterfaceWizard extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // middleware ensures superuser access only so always authorised here:
        return Auth::getUser()->isSuperUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cust'                  => 'required|integer',
            'vlan'                  => 'required|integer',
            'switch'                => 'required|integer',
            'switch-port'           => 'required|integer',
            'status'                => 'required|integer|in:' . implode( ',', array_keys( PhysicalInterfaceEntity::$STATES ) ),
            'speed'                 => 'required|integer|in:' . implode( ',', array_keys( PhysicalInterfaceEntity::$SPEED ) ),
            'duplex'                => 'required|string|in:' . implode( ',', array_keys( PhysicalInterfaceEntity::$DUPLEX ) ),
            'maxbgpprefix'          => 'integer|nullable',
            'ipv4-address'          => 'ipv4' . ( $this->input('ipv4-enabled') ? '|required' : '|nullable' ),
            'ipv6-address'          => 'ipv6' . ( $this->input('ipv6-enabled') ? '|required' : '|nullable' ),
        ];
    }
}