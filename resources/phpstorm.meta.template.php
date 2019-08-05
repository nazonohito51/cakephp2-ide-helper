
namespace PHPSTORM_META {
<?php /** @var \CakePhp2IdeHelper\OverRideEntry $overrideEntry */ foreach ($overrideEntries as $overrideEntry) : ?>
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
}
