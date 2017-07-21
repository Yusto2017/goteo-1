<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $vars['project'];
$errors = $project->errors['supports'] ?: array();
$okeys  = $project->okeys['supports'] ?: array();
$errors = $vars['errors'];
$vars['level'] = 3;

$support_types = array();

foreach ($vars['types'] as $id => $type) {
    $support_types[] = array(
        'value' => $id,
        'class' => "support_{$id}",
        'label' => $type
    );
}

$supports = array();

foreach ($project->supports as $support) {

    $ch = array();

    // a ver si es el que estamos editando o no
    if (!empty($vars["support-{$support->id}-edit"])) {


        $support_types = array();

        foreach ($vars['types'] as $id => $type) {
            $support_types["support-{$support->id}-type-{$id}"] = array(
                'name'  => "support-{$support->id}-type",
                'value' => $id,
                'type'  => 'radio',
                'class' => "support-type support_{$id}",
                'hint'  => Text::get('tooltip-project-support-type-'.$id),
                'label' => $type,
                'checked' => $id == $support->type  ? true : false
            );
        }


        // a este grupo le ponemos estilo de edicion
    $supports["support-{$support->id}"] = array(
            'type'      => 'group',
            'class'     => 'support editsupport',
            'children'  => array(

                "support-{$support->id}-edit" => array(
                    'type' => 'hidden',
                    'value' => 1
                ),

                "support-{$support->id}-support" => array(
                    'title'     => Text::get('supports-field-support'),
                    'type'      => 'textbox',
                    'required'  => true,
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $support->support,
                    'errors'    => !empty($errors["support-{$support->id}-support"]) ? array($errors["support-{$support->id}-support"]) : array(),
                    'ok'        => !empty($okeys["support-{$support->id}-support"]) ? array($okeys["support-{$support->id}-support"]) : array(),
                    'hint'      => Text::get('tooltip-project-support-support'),
                ),
                "support-{$support->id}-type" => array(
                    'title'     => Text::get('supports-field-type'),
                    'required'  => true,
                        'class'     => 'inline',
                        'type'      => 'group',
                        'value'     => $support->type,
                        'children'  => $support_types,
                    'errors'    => !empty($errors["support-{$support->id}-type"]) ? array($errors["support-{$support->id}-type"]) : array(),
                    'ok'        => !empty($okeys["support-{$support->id}-type"]) ? array($okeys["support-{$support->id}-type"]) : array(),
                    'hint'      => Text::get('tooltip-project-support-type'),
                ),
                "support-{$support->id}-description" => array(
                    'type'      => 'textarea',
                    'required'  => true,
                    'title'     => Text::get('supports-field-description'),
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline support-description',
                    'value'     => $support->description,
                    'errors'    => !empty($errors["support-{$support->id}-description"]) ? array($errors["support-{$support->id}-description"]) : array(),
                    'ok'        => !empty($okeys["support-{$support->id}-description"]) ? array($okeys["support-{$support->id}-description"]) : array(),
                    'hint'      => Text::get('tooltip-project-support-description')
                ),
                "support-{$support->id}-buttons" => array(
                    'type' => 'group',
                    'class' => 'buttons',
                    'children' => array(
                        "support-{$support->id}-ok" => array(
                            'type'  => 'submit',
                            'label' => Text::get('form-accept-button'),
                            'class' => 'inline ok'
                        ),
                        "support-{$support->id}-remove" => array(
                            'type'  => 'submit',
                            'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove red'
                            )
                        )
                    )
                )
            );

    } else {

        $supports["support-{$support->id}"] = array(
            'class'     => 'support',
            'view'      => 'project/edit/supports/support.html.php',
            'data'      => array('support' => $support),
        );
    }


}


$sfid = 'sf-project-supports';

?>

<form method="post" action="/dashboard/projects/supports/save" class="project" enctype="multipart/form-data">

<?php echo SuperForm::get(array(

    'id'            => $sfid,

    'action'        => '',
    'level'         => $vars['level'],
    'method'        => 'post',
    'title'         => '',
    'hint'          => Text::get('guide-project-supports'),
    'class'         => 'aqua',
    /*
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-supports',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    */
    'elements'      => array(
        'process_supports' => array (
            'type' => 'hidden',
            'value' => 'supports'
        ),
        'supports' => array(
            'type'      => 'group',
            'title'     => Text::get('supports-fields-support-title'),
            'hint'      => Text::get('tooltip-project-supports'),
            'children'  => $supports + array(
                'support-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add support-add red',
                )
            )
        )
    )

));
?>
</form>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function () {

    var supports = $('div#<?php echo $sfid ?> li.element#li-supports');

    supports.delegate('li.element.support input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        supports.superform({data:data});
    });

    supports.delegate('li.element.editsupport input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        supports.superform({data:data});
    });

    supports.delegate('li.element.editsupport input.remove, li.element.support input.remove', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        supports.superform({data:data});
    });

    supports.delegate('#li-support-add input', 'click', function (event) {
       event.preventDefault();
       var data = {};
       data[this.name] = '1';
       supports.superform({data:data});
    });

});
// @license-end
</script>
