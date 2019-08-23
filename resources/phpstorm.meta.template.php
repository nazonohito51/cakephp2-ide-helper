
namespace PHPSTORM_META {
<?php /** @var \CakePhp2IdeHelper\PhpStormMeta\OverRideEntry $overrideEntry */ foreach ($overrideEntries as $overrideEntry) : ?>
    override(
        <?php echo $overrideEntry->getTarget();?>,
        map(
            array(
        <?php foreach ($overrideEntry->getMap() as $key => $value) : ?>
        '<?php echo $key;?>' => '<?php echo $value;?>',
        <?php endforeach; ?>
    )
        )
    );
<?php endforeach; ?>

<?php /** @var \CakePhp2IdeHelper\PhpStormMeta\ExpectArgumentsEntry $expectArgumentsEntry */ foreach ($expectArgumentsEntries as $expectArgumentsEntry) : ?>
    expectedArguments(
        <?php echo $expectArgumentsEntry->getTarget();?>,
        <?php echo $expectArgumentsEntry->getArgPosition();?>,
<?php foreach ($expectArgumentsEntry->getArgs() as $value) : ?>
        <?php echo $value;?>,
<?php endforeach; ?>
    );
<?php endforeach; ?>
}
