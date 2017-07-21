<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$project = $vars['project'];
$user = $vars['user'];

$interests = array();

$errors = $project->errors[$vars['step']] ?: array();
$okeys  = $project->okeys[$vars['step']] ?: array();

foreach ($vars['interests'] as $value => $label) {
    $interests[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $user->interests)
        );
}

$user_webs = array();

$birthyear_options=array();

$years =  range(date('Y') - 18, date('Y') - 100 );

$birthyear_options[]= array(
            'value'     => '',
            'label'     => Text::get('invest-address-birthyear-field')
        );

foreach ($years as $year) {
    $birthyear_options[] =  array(
            'value'     => $year,
            'label'     => $year,
        );
}


$legal_entity_options=[
    [   'value' => '' ,
        'label' => Text::get('profile-field-legal-entity-choose')
    ],
    [   'value' => '0' ,
        'label' => Text::get('profile-field-legal-entity-person')
    ],
    [   'value' => '1' ,
        'label' => Text::get('profile-field-legal-entity-self-employed')
    ],
    [   'value' => '2' ,
        'label' => Text::get('profile-field-legal-entity-ngo')
    ],
    [   'value' => '3' ,
        'label' => Text::get('profile-field-legal-entity-company')
    ],
    [   'value' => '4' ,
        'label' => Text::get('profile-field-legal-entity-cooperative')
    ],
    [   'value' => '5' ,
        'label' => Text::get('profile-field-legal-entity-asociation')
    ],
    [   'value' => '6' ,
        'label' => Text::get('profile-field-legal-entity-others')
    ],
];


foreach ($user->webs as $web) {

    $ch = array();

    // a ver si es el que estamos editando o no
    if (!empty($vars["web-{$web->id}-edit"])) {

        $user_webs["web-{$web->id}"] = array(
            'type'      => 'group',
            'class'     => 'web editweb',
            'children'  => array(
                    "web-{$web->id}-edit" => array(
                        'type'  => 'hidden',
                        'class' => 'inline',
                        'value' => '1'
                    ),
                    'web-' . $web->id . '-url' => array(
                        'type'      => 'textbox',
                        'required'  => true,
                        'title'     => Text::get('profile-field-url'),
                        'value'     => $web->url,
                        'hint'      => Text::get('tooltip-user-webs'),
                        'errors'    => !empty($errors['web-' . $web->id . '-url']) ? array($errors['web-' . $web->id . '-url']) : array(),
                        'ok'        => !empty($okeys['web-' . $web->id . '-url']) ? array($okeys['web-' . $web->id . '-url']) : array(),
                        'class'     => 'web-url inline no-autoupdate'
                    ),
                    "web-{$web->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'inline buttons',
                        'children' => array(
                            "web-{$web->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            ),
                            "web-{$web->id}-remove" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove weak'
                            )
                        )
                    )
                )
        );

    } else {

        $user_webs["web-{$web->id}"] = array(
            'class'     => 'web',
            'view'      => 'project/edit/webs/web.html.php',
            'data'      => array('web' => $web),
        );

    }

}

$sfid = 'sf-project-profile';

echo SuperForm::get(array(
    'id'            => $sfid,
    'action'        => '',
    'level'         => $vars['level'],
    'method'        => 'post',
    'title'         => Text::get('profile-main-header'),
    'hint'          => Text::get('guide-project-user-information'),
    'elements'      => array(
        'process_userProfile' => array (
            'type' => 'hidden',
            'value' => 'userProfile'
        ),

        'anchor-profile' => array(
            'type' => 'html',
            'html' => '<a name="profile"></a>'
        ),

        'user_name' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => Text::get('profile-field-name'),
            'hint'      => Text::get('tooltip-user-name'),
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array(),
            'value'     => $user->name
        ),
        'user_location' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => Text::get('profile-field-location'),
            'hint'      => Text::get('tooltip-user-location'),
            'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
            'ok'        => !empty($okeys['location']) ? array($okeys['location']) : array(),
            //google autocomplete
            'class'     => 'geo-autocomplete',
            //write data to location tables
            'data'      => array('geocoder-type' => 'user'),
            'value'     => $user->location
        ),

        'user_birthyear' => array(
            'type'      => 'Select',
            'title'     => Text::get('invest-address-birthyear-field'),
            'hint'      => '',
            'errors'    => !empty($errors['birthyear']) ? array($errors['birthyear']) : array(),
            'ok'        => !empty($okeys['birthyear']) ? array($okeys['birthyear']) : array(),
            'options'   => $birthyear_options,
            'value'     => $user->birthyear
        ),

        "user_gender"  => array(
                        'title'     => Text::get('invest-address-gender-field'),
                        'class'     => 'inline cost-required cols_2',
                        'type'      => 'radios',
                        'options'   => array (
                            array(
                                    'value'     => 'M',
                                    'class'     => 'required_cost-yes',
                                    'label'     => Text::get('regular-male')
                                ),
                            array(
                                    'value'     => 'F',
                                    'class'     => 'required_cost-no',
                                    'label'     => 'Dos rondas',
                                    'label'     => Text::get('regular-female')
                                )
                        ),
                        'value'     => $user->gender,
                        'errors'    => array(),
                        'ok'        => array(),
                        'hint'      => ''
        ),

        'user_legal_entity' => array(
            'type'      => 'Select',
            'title'     => Text::get('profile-field-legal-entity'),
            'hint'      => '',
            'errors'    => !empty($errors['legal_entity']) ? array($errors['legal_entity']) : array(),
            'ok'        => !empty($okeys['legal_entity']) ? array($okeys['legal_entity']) : array(),
            'options'   => $legal_entity_options,
            'value'     => $user->legal_entity
        ),

        'anchor-avatar' => array(
            'type' => 'html',
            'class' => 'inline',
            'html' => '<a name="avatar"></a>'
        ),

        'user_avatar' => array(
            'type'      => 'group',
            'required'  => true,
            'title'     => Text::get('profile-fields-image-title'),
            'hint'      => Text::get('tooltip-user-image'),
            'errors'    => !empty($errors['avatar']) ? array($errors['avatar']) : array(),
            'ok'        => !empty($okeys['avatar']) ? array($okeys['avatar']) : array(),
            'class'     => 'user_avatar',
            'children'  => array(
                'avatar_upload'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline avatar_upload',
                    'hint'  => Text::get('tooltip-user-image'),
                    'onclick' => "document.getElementById('proj-superform').action += '#avatar';"
                ),
                'avatar-current' => array(
                    'type' => 'hidden',
                    'value' => $user->avatar->id == 'la_gota.png' ? '' : $user->avatar->id,
                ),
                'avatar-image' => array(
                    'type'  => 'html',
                    'class' => 'inline avatar-image',
                    'html'  => is_object($user->avatar) &&  $user->avatar->id != 'la_gota.png' ?
                               '<img src="' . SITE_URL . '/image/' . $user->avatar->id . '/128/128" alt="Avatar" /><button class="image-remove" type="submit" name="avatar-'.$user->avatar->hash.'-remove" title="Quitar imagen" value="remove">X</button>' :
                               ''
                )

            )
        ),

        'user_about' => array(
            'type'      => 'textarea',
            'required'  => true,
            'cols'      => 40,
            'rows'      => 4,
            'title'     => Text::get('profile-field-about'),
            'hint'      => Text::get('tooltip-user-about'),
            'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
            'ok'        => !empty($okeys['about']) ? array($okeys['about']) : array(),
            'value'     => $user->about
        ),
        'interests' => array(
            'type'      => 'checkboxes',
            'required'  => true,
            'class'     => 'cols_3',
            'name'      => 'user_interests[]',
            'title'     => Text::get('profile-field-interests'),
            'hint'      => Text::get('tooltip-user-interests'),
            'errors'    => !empty($errors['interests']) ? array($errors['interests']) : array(),
            'ok'        => !empty($okeys['interests']) ? array($okeys['interests']) : array(),
            'options'   => $interests
        ),

        'anchor-webs' => array(
            'type' => 'html',
            'html' => '<a name="webs"></a>'
        ),

        'user_webs' => array(
            'type'      => 'group',
            'required'  => false,
            'title'     => Text::get('profile-field-websites'),
            'hint'      => Text::get('tooltip-user-webs'),
            'class'     => 'webs',
            'errors'    => !empty($errors['webs']) ? array($errors['webs']) : array(),
            'ok'        => !empty($okeys['webs']) ? array($okeys['webs']) : array(),
            'children'  => $user_webs + array(
                'web-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add red'
                )
            )
        ),
        'user_social' => array(
            'type'      => 'group',
            'title'     => Text::get('profile-fields-social-title'),
            'children'  => array(
                'user_facebook' => array(
                    'type'      => 'textbox',
                    'class'     => 'facebook',
                    'size'      => 40,
                    'title'     => Text::get('regular-facebook'),
                    'hint'      => Text::get('tooltip-user-facebook'),
                    'errors'    => !empty($errors['facebook']) ? array($errors['facebook']) : array(),
                    'ok'        => !empty($okeys['facebook']) ? array($okeys['facebook']) : array(),
                    'value'     => empty($user->facebook) ? Text::get('regular-facebook-url') : $user->facebook
                ),
                'user_google' => array(
                    'type'      => 'textbox',
                    'class'     => 'google',
                    'size'      => 40,
                    'title'     => Text::get('regular-google'),
                    'hint'      => Text::get('tooltip-user-google'),
                    'errors'    => !empty($errors['google']) ? array($errors['google']) : array(),
                    'ok'        => !empty($okeys['google']) ? array($okeys['google']) : array(),
                    'value'     => empty($user->google) ? Text::get('regular-google-url') : $user->google
                ),
                'user_twitter' => array(
                    'type'      => 'textbox',
                    'class'     => 'twitter',
                    'size'      => 40,
                    'title'     => Text::get('regular-twitter'),
                    'hint'      => Text::get('tooltip-user-twitter'),
                    'errors'    => !empty($errors['twitter']) ? array($errors['twitter']) : array(),
                    'ok'        => !empty($okeys['twitter']) ? array($okeys['twitter']) : array(),
                    'value'     => empty($user->twitter) ? Text::get('regular-twitter-url') : $user->twitter
                ),
                'user_identica' => array(
                    'type'      => 'textbox',
                    'class'     => 'identica',
                    'size'      => 40,
                    'title'     => Text::get('regular-identica'),
                    'hint'      => Text::get('tooltip-user-identica'),
                    'errors'    => !empty($errors['identica']) ? array($errors['identica']) : array(),
                    'ok'        => !empty($okeys['identica']) ? array($okeys['identica']) : array(),
                    'value'     => empty($user->identica) ? Text::get('regular-identica-url') : $user->identica
                ),
                'user_linkedin' => array(
                    'type'      => 'textbox',
                    'class'     => 'linkedin',
                    'size'      => 40,
                    'title'     => Text::get('regular-linkedin'),
                    'hint'      => Text::get('tooltip-user-linkedin'),
                    'errors'    => !empty($errors['linkedin']) ? array($errors['linkedin']) : array(),
                    'ok'        => !empty($okeys['linkedin']) ? array($okeys['linkedin']) : array(),
                    'value'     => empty($user->linkedin) ? Text::get('regular-linkedin-url') : $user->linkedin
                )
            )
        ),
         'user_analytic' => array(
            'type'      => 'group',
            'title'     => Text::get('profile-fields-analytics-title'),
            'children'  => array(
                'analytics_id' => array(
                    'type'      => 'textbox',
                    'size'      => 40,
                    'title'     => Text::get('regular-analytics'),
                    'hint'      => Text::get('tooltip-user-analytics'),
                    'errors'    => !empty($errors['analytics_id']) ? array($errors['analytics_id']) : array(),
                    'ok'        => !empty($okeys['analytics_id']) ? array($okeys['analytics_id']) : array(),
                    'value'     => empty($project->analytics_id) ? '' : $project->analytics_id
                ),
                'facebook_pixel' => array(
                    'type'      => 'textbox',
                    'size'      => 40,
                    'title'     => Text::get('regular-facebook-pixel'),
                    'hint'      => Text::get('tooltip-user-facebook-pixel'),
                    'errors'    => !empty($errors['facebook_pixel']) ? array($errors['facebook_pixel']) : array(),
                    'ok'        => !empty($okeys['facebook_pixel']) ? array($okeys['facebook_pixel']) : array(),
                    'value'     => empty($project->facebook_pixel) ? '' : $project->facebook_pixel
                )
            )
        ),
        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('project/edit/errors.html.php', array(
                        'project'   => $project,
                        'step'      => $vars['step']
                    ))
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-'.$vars['next'],
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )
        )
    )
));
?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function () {

    var webs = $('div#<?php echo $sfid ?> li.element#li-user_webs');

    webs.delegate('li.element.web input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        webs.superform({data:data});
    });

    webs.delegate('li.element.editweb input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        webs.superform({data:data});
    });

    webs.delegate('li.element.editweb input.remove, li.element.web input.remove', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        webs.superform({data:data});
    });

    webs.delegate('#li-web-add input', 'click', function (event) {
       event.preventDefault();
       var data = {};
       data[this.name] = '1';
       webs.superform({data:data});
    });

});
// @license-end
</script>
