<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Sflow Receivers / <?= $t->sflr ? 'Edit' : 'Add' ?> Sflow Receiver
<?php $this->append() ?>



<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'interfaces/sflow-receiver/list' ) ?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
    </div>
<?php $this->append() ?>


<?php $this->section('content') ?>

    <div class="row">

        <div class="col-lg-12">

            <?= $t->alerts() ?>

            <div class="card">
                <div class="card-body">
                    <?= Former::open()->method( 'post' )
                        ->action( route( 'sflow-receiver@store' ) )
                        ->customInputWidthClass( 'col-sm-6 col-lg-4' )
                        ->customLabelWidthClass( 'col-lg-2 col-md-4 col-sm-4' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                    <?= Former::text( 'dst_ip' )
                        ->label( 'Destination IP' )
                        ->blockHelp( 'help text' );
                    ?>

                    <?= Former::number( 'dst_port' )
                        ->label( 'Destination Port' )
                        ->blockHelp( 'help text' );
                    ?>

                    <?= Former::hidden( 'id' )
                        ->value( $t->sflr ? $t->sflr->getId() : null )
                    ?>

                    <?= Former::hidden( 'viid' )
                        ->value( $t->sflr ? $t->sflr->getVirtualInterface()->getId() : $t->vi->getId() )
                    ?>

                    <?=Former::actions(
                        Former::primary_submit( $t->sflr ? 'Save Changes' : 'Add' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href( $t->vi ? route(  'interfaces/virtual/edit' , [ 'id' => $t->vi->getId() ] ) :  route( 'interfaces/sflow-receiver/list' ) )->class( "mb-2 mb-sm-0" ),
                        Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                    )->id('btn-group');?>

                    <?= Former::close() ?>
                </div>
            </div>

            <div class="alert alert-info mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-info-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        <h3>Sflow Receivers / Exporting Sflow Telemetry</h3>

                        <span>
                            This feature allows you to export sflow telemetry to IXP participants using PMacct. Please see
                            <a href="https://www.ixpmanager.org/media/2016/201610-ripe73-inex-nh-exporting-sflow.pdf">these slides</a>
                            and <a href="https://ripe73.ripe.net/archives/video/1458/">this video</a> from Nick Hilliard at RIPE73
                            for more details.
                        </span>
                    </div>
                </div>
            </div>

        </div>

    </div>

<?php $this->append() ?>
