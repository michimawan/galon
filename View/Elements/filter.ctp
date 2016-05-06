<?php
echo $this->Form->create($model, ['action' => $action, 'controllers' => $controllers, 'class' => 'form-inline', 'type' => 'get']);
echo $this->Form->input('filter', [
    'label' => false,
    'class' => 'form-control',
    'div' => ['class' => 'form-group'],
    'type' => 'select',
    'options' => $filters
]);
echo $this->Form->input('text', [
    'label' => false,
    'class' => 'form-control',
    'div' => ['class' => 'form-group'],
    'type' => 'text',
    'placeholder' => 'your text here'
]);
echo $this->Form->button('Filter', ['type' => 'submit', 'class' => 'form-submit btn btn-default',  'title' => 'Filter', 'div' => false]);
echo $this->Form->end();
?>
