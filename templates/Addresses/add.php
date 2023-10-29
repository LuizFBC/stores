<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Address $address
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Addresses'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="addresses form content">
            <?= $this->Form->create($address) ?>
            <fieldset>
                <legend><?= __('Add Address') ?></legend>
                <?php
                    echo $this->Form->control('foreign_table');
                    echo $this->Form->control('foreign_id');
                    echo $this->Form->control('postal_code');
                    echo $this->Form->control('state');
                    echo $this->Form->control('city');
                    echo $this->Form->control('sublocality');
                    echo $this->Form->control('street');
                    echo $this->Form->control('street_number');
                    echo $this->Form->control('complement');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
