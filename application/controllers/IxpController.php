<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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


/**
 * Controller: Manage IXPs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IxpController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        if( !$this->multiIXP() )
        {
            $this->addMessage(
                'Multi-IXP mode has not been enabled. '
                    . 'Please see <a href="https://github.com/inex/IXP-Manager/wiki/Multi-IXP-Functionality">this page</a> '
                    . 'for more information and documentation.',
                OSS_Message::ERROR
            );
            $this->redirectAndEnsureDie();
        }

        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\IXP',
            'form'          => 'IXP_Form_IXP',
            'pagetitle'     => 'IXPs',

            'titleSingular' => 'IXP',
            'nameSingular'  => 'a IXP',

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC'
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id'        => [ 'title' => 'UID', 'display' => false ],
                    'name'      => 'Name',
                    'shortname' => 'Shortname',
                ];

                // display the same information in the view as the list
                $this->_feParams->viewColumns = $this->_feParams->listColumns;

                $this->_feParams->defaultAction = 'list';
                break;

            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
    }

    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'i.id AS id, i.name AS name,
                i.shortname AS shortname, i.address1 AS address1, i.address2 AS address2,
                i.address3 AS address3, i.address4 AS address4, i.country AS country'
            )
            ->from( '\\Entities\\IXP', 'i' );

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'i.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
    }
    
    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * You can stop the deletion by returning false but you should also add a
     * message to explain why.
     *
     * @param object $object The Doctrine2 entity to delete
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete( $object )
    {
        if( ( $cnt = count( $object->getInfrastructures() ) ) )
        {
            $this->addMessage(
                    "Could not delete this IXP as {$cnt} infrastructures(es) are associated with it",
                    OSS_Message::ERROR
            );
            return false;
        }
    
        if( ( $cnt = count( $object->getCustomers() ) ) )
        {
            $this->addMessage(
                    "Could not delete this IXP as {$cnt} customer(es) are associated with it",
                    OSS_Message::ERROR
            );
            return false;
        }
    
        return true;
    }
    
    /**
     * Post database flush hook that can be overridden by subclasses and is called by
     * default for a successful add / edit / delete.
     *
     * Called by `addPostFlush()` and `postDelete()` - if overriding these, ensure to
     * call this if you have overridden it.
     *
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @return bool
     */
    protected function postFlush( $object )
    {
        // wipe cached entries
        if( $object->getId() == 1 )
            $this->getD2Cache()->delete( \Repositories\IXP::CACHE_KEY_DEFAULT_IXP );
        return true;
    }
    
}
