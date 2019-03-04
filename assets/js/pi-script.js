jQuery(document).ready(function($){
    randomnum();
    //Form Scripts
    $(".pi-choose").click( function(e) {
        $(".choose-wrap").slideToggle("fast");   
    });
    $(".pi-close").click( function(e) {
        $(".choose-wrap").slideToggle("fast");   
    });
    //On Click. Do a Final Check and go. Contact Page Form 
    $('#pi-submit').on('click', function(e){
        e.preventDefault();
        
        var me = $(this);
        var form = me.closest('.pi-form').find('form');
        if ( me.data('requestRunning') ) {
            return;
        }
        me.data('requestRunning', true);

        var formData = $('.pi-form form').serialize();
        var total = parseInt( $('.rand1').html() ) + parseInt( $('.rand2').html() );

        var data = {
            action: 'pi_form_ajaxhandler',
            formData: formData,
            total, total,
            nonce:  pi_msg_ajax.nonce
        };
        

        $.post(
            pi_msg_ajax.ajaxURL,
            data
        ).complete( function () {
            //When complete do this
        }).success( function ( response ) {
            //let user know that the claim process has begun and what is next
            if( response.success == false ){
                me.closest('.pi-form').find('.form-box').remove();
                me.closest('.pi-form').find('form').prepend('<span class="form-box alert alert-danger"></span>').fadeIn('slow');
                $.each( response.data, function( index, item ) {
                    $('.form-box').append( item + '<br>');
                });
            }else{
                me.closest('.pi-form').find('.form-box').remove().fadeOut('slow');
                form.after('<span class="alert alert-success">' + response.data + '</span>').remove().fadeOut('slow');
                form.delay( 3000 ).fadeOut( 400 ).remove();
                console.log( response );
            }
        }).fail( function ( xhr ) {
            //Let user know it failed and why. Are there other options?
            console.log( pi_msg_ajax.failMessage + xhr.status );
        }); 
    });

    //All Modal Scripts
    //On Blur of required fields
    $('.pi-modal').find('.required').on('blur', function(e){
        var me = $(this);
        var formData = $(this).serialize();
        var total = parseInt( $('.rand1').html() ) + parseInt( $('.rand2').html() );

        var data = {
            action: 'pi_input_ajaxhandler',
            formData: formData,
            total: total,
            nonce:  pi_msg_ajax.nonce
        };
        $.post(
            pi_msg_ajax.ajaxURL,
            data
        ).success( function ( response ) {

            if( response.success == false ){
                me.closest('.form-group').find('.form-alert').remove().fadeOut('slow');
                me.after('<span class="form-alert alert alert-danger">' +  response.data + '</span>').fadeIn('slow');
            }else{
                me.closest('.form-group').find('.form-alert').remove().fadeOut('slow');
                me.closest('.pi-form').find('.form-box').remove().fadeOut('slow');
                me.addClass('success').fadeIn('slow');
            }
            //let user know that the claim process has begun and what is next
        }).fail( function ( xhr ) {
            //Let user know it failed and why. Are there other options?
            console.log( pi_msg_ajax.failMessage + xhr.status );
            
        }); 
    });
    //Self assesment questionnaires
    $('#self-assesment').off().on('click', function(e){
        e.preventDefault();
        
        var me = $(this);
        var counter = 0;
        if ( me.data('requestRunning') ) {
            return;
        }
        me.data('requestRunning', true);
        
        me.closest('#ac-tabs').find('input[type*=radio]:checked').each(function() {
            if ($(this).val() == 'Yes'){
                counter++;
            }
        });
        var formData = me.closest('.pi-modal').find('form').serialize();

        var number = $(this).closest('.ac-tab').data('number');
        var next = number + 1;

        var data = {
            action: 'pi_questionnaire_ajaxhandler',
            formData: formData,
            nonce:  pi_msg_ajax.nonce
        };

        $.post(
            pi_msg_ajax.ajaxURL,
            data
        ).complete( function () {
            //When complete do this
        }).success( function ( response ) {
            $('#tabs-' + number).addClass('inactive');
            $('#tabs-' + next).removeClass('inactive').addClass('active');
            $('#tab-btn-' + number).removeClass('active');
            $('#tab-btn-' + next).addClass('active');
            $('#ac-tab-total').html(counter);
            
            if( counter < 3 ){
                $('.ac-tab-msg').html('No Apparent Issue').addClass('no-risk');
                $('.ac-tab-final').html('Your answers to this questionnaire suggest that you are in the normal range and therefore, at low risk of developing a substance abuse problem.').addClass('no-risk');
                 $('.ac-tab-total').addClass('no-risk');
                 $( '<a href="https://www.whitesandstreatment.com" class="btn btn-primary">Return Home</a>' ).insertAfter( ".ac-tab-final" );
            }else if( counter >= 3 && counter <= 5 ){
                $('.ac-tab-msg').html('You are at risk!').addClass('at-risk');
                $('.ac-tab-final').html('Your answers to this questionnaire suggest that you may be at risk of substance abuse. We recommend that you <strong>contact an addiction specialist</strong> before this habit develops into something even more serious.').addClass('at-risk');
                $('.ac-tab-total').addClass('at-risk');
                $( '<a href="https://www.whitesandstreatment.com/find-treatment/" class="btn btn-primary">Find Local Help</a>' ).insertAfter( ".ac-tab-final" );
                $( '<a href="https://www.whitesandstreatment.com/contact/" class="btn btn-primary">Contact Us Today</a>' ).insertAfter( ".ac-tab-final" );
            }else if( counter > 5 ){
                $('.ac-tab-msg').html('High Risk!').addClass('high-risk');
                $('.ac-tab-final').html('Your answers to this questionnaire suggest that you are at high risk of developing an addiction. We strongly recommend that you <strong>contact an addiction specialist</strong> before this problem spirals even further out of your control.').addClass('high-risk');
                 $('.ac-tab-total').addClass('high-risk');
                 $( '<a href="' + response.data + '/contact/" class="btn btn-primary">Get Help Now</a>' ).insertAfter( ".ac-tab-final" );
            }                        
        }).fail( function ( xhr ) {
            //Let user know it failed and why. Are there other options?
            console.log( pi_msg_ajax.failMessage + xhr.status );
        }); 
    });
    
    $('#aa-self-ass').off().on('click', function(e){
        e.preventDefault();
        
        var me = $(this);
        var counter = 0;
        if ( me.data('requestRunning') ) {
            return;
        }
        me.data('requestRunning', true);
        
        me.closest('#aa-tabs').find('input[type*=radio]:checked').each(function() {
            if ($(this).val() == 'Yes'){
                counter++;
            }
        });
        var formData = me.closest('.pi-modal').find('form').serialize();

        var number = me.closest('.ac-tab').data('number');
        var next = number + 1;

        var data = {
            action: 'pi_questionnaire_ajaxhandler',
            formData: formData,
            nonce:  pi_msg_ajax.nonce
        };

        $.post(
            pi_msg_ajax.ajaxURL,
            data
        ).complete( function () {
            //When complete do this
        }).success( function ( response ) {
            $('#aa-tabs-' + number).addClass('inactive');
            $('#aa-tabs-' + next).removeClass('inactive').addClass('active');
            $('#aa-tab-btn-' + number).removeClass('active');
            $('#aa-tab-btn-' + next).addClass('active');
            $('#aa-tab-total').html(counter);
            
            if( counter < 3 ){
                $('.ac-tab-msg').html('No Apparent Issue').addClass('no-risk');
                $('.ac-tab-final').html('Your answers to this questionnaire suggest that you are in the normal range and therefore, at low risk of developing a substance abuse problem.').addClass('no-risk');
                 $('.ac-tab-total').addClass('no-risk');
                 $( '<a href="'+ response.data +'" class="btn btn-primary">Return Home</a>' ).insertAfter( ".ac-tab-final" );
            }else if( counter >= 3 && counter <= 5 ){
                $('.ac-tab-msg').html('You are at risk!').addClass('at-risk');
                $('.ac-tab-final').html('Your answers to this questionnaire suggest that you may be at risk of substance abuse. We recommend that you <strong>contact an addiction specialist</strong> before this habit develops into something even more serious.').addClass('at-risk');
                $('.aa-tab-total').addClass('at-risk');
                $( '<a href="' + response.data + '/contact/" class="btn btn-primary">Contact Us Today</a>' ).insertAfter( ".ac-tab-final" );
            }else if( counter > 5 ){
                $('.ac-tab-msg').html('High Risk!').addClass('high-risk');
                $('.ac-tab-final').html('Your answers to this questionnaire suggest that you are at high risk of developing an addiction. We strongly recommend that you <strong>contact an addiction specialist</strong> before this problem spirals even further out of your control.').addClass('high-risk');
                 $('.ac-tab-total').addClass('high-risk');
                 $( '<a href="' + response.data + '/contact/" class="btn btn-primary">Get Help Now</a>' ).insertAfter( ".ac-tab-final" );
            }                        
        }).fail( function ( xhr ) {
            //Let user know it failed and why. Are there other options?
            console.log( pi_msg_ajax.failMessage + xhr.status );
        }); 
    });
    $(".first-next").click(function () {

        var me = $(this);
        
        var name = me.closest('.ac-tab').find( "input[name*='pi_name']" ).val();
        var phone = me.closest('.ac-tab').find( "input[name*='pi_phone']" ).val();
        var email = me.closest('.ac-tab').find( "input[name*='pi_email']" ).val();

        var number = me.closest('.ac-tab').data('number');
        var next = number + 1;

        var data = {
            action: 'pi_next_ajaxhandler',
            name: name,
            phone: phone,
            email: email,
            nonce:  pi_msg_ajax.nonce
        };
        $.post(
            pi_msg_ajax.ajaxURL,
            data
        ).success( function ( response ) {

            if( response.success == false ){
                me.closest('.ac-tab').find('.errors').remove();
                $( "<p class='errors'>Please fill out all required fields</p>" ).insertAfter( me );
            }else{
                //Drug Form
                me.closest('.ac-tab').find('.errors').remove();
                me.closest('.ac-tab').addClass('inactive');
                $('#tabs-' + next).removeClass('inactive').addClass('active');
                $('#tab-btn-' + number).removeClass('active');
                $('#tab-btn-' + next).addClass('active');

                //Alcohol Form
                me.closest('.ac-tab').find('.errors').remove();
                me.closest('.ac-tab').addClass('inactive');
                $('#aa-tabs-' + next).removeClass('inactive').addClass('active');
                $('#aa-tab-btn-' + number).removeClass('active');
                $('#aa-tab-btn-' + next).addClass('active');
            }
            //let user know that the claim process has begun and what is next
        }).fail( function ( xhr ) {
            //Let user know it failed and why. Are there other options?
            console.log( pi_msg_ajax.failMessage + xhr.status );
            
        });
    });
    $(".ac-next").click(function () {
        var number = $(this).closest('.ac-tab').data('number');
        var next = number + 1;

        $(this).closest('.ac-tab').find('.errors').remove();
        $(this).closest('.ac-tab').addClass('inactive');
        $('#tabs-' + next).removeClass('inactive').addClass('active');
        $('#tab-btn-' + number).removeClass('active');
        $('#tab-btn-' + next).addClass('active');

    });
    $(".ac-previous").click(function () {
        var number = $(this).closest('.ac-tab').data('number');
        var prev = number - 1;
        $(this).closest('.ac-tab').find('.errors').remove();
        $(this).closest('.ac-tab').addClass('inactive');
        $('#tabs-' + prev).removeClass('inactive').addClass('active');
        $('#tab-btn-' + number).removeClass('active');
        $('#tab-btn-' + prev).addClass('active');
    });

    //  Make the toggle for the Homepage 
    $("#view-more").click(function () {
       $('#expand-therapies').slideToggle("slow");
       
       if ( $(this).find('.glyphicon-circle-arrow-down').hasClass("box-rotate")) {
            $(this).find('.glyphicon-circle-arrow-down').removeClass('box-rotate');
        }else{
            $(this).find('.glyphicon-circle-arrow-down').addClass('box-rotate box-transition');
        }
       
       return false;
    });
    $(".aa-next").click(function () {
        var number = $(this).closest('.ac-tab').data('number');
        var next = number + 1;
        $(this).closest('.ac-tab').find('.errors').remove();
        $(this).closest('.ac-tab').addClass('inactive');
        $('#aa-tabs-' + next).removeClass('inactive').addClass('active');
        $('#aa-tab-btn-' + number).removeClass('active');
        $('#aa-tab-btn-' + next).addClass('active');      
    });
    $(".aa-previous").click(function () {
        var number = $(this).closest('.ac-tab').data('number');
        var prev = number - 1;
        $(this).closest('.ac-tab').find('.errors').remove();
        $(this).closest('.ac-tab').addClass('inactive');
        $('#aa-tabs-' + prev).removeClass('inactive').addClass('active');
        $('#aa-tab-btn-' + number).removeClass('active');
        $('#aa-tab-btn-' + prev).addClass('active');
    });
    //Submit comment via ajax
    $('#survey-submit').on('click', function(e){
        e.preventDefault();
        
        var me = $(this);
        if ( me.data('requestRunning') ) {
            return;
        }
        me.data('requestRunning', true);

        var formData = $('.pi-survey').serialize();
        var surveyType = $('#survey-type').val();

        var data = {
            action: 'submit_survey',
            formData: formData,
            surveyType: surveyType,
            nonce:  pi_msg_ajax.nonce
        };
        $.post(
            pi_msg_ajax.ajaxURL,
            data
        ).complete( function () {
            //Run when completed
            me.data('requestRunning', false);
        }).success( function ( response ) {
            //let user know that the claim process has begun and what is next
            // $( response.data ).insertAfter( "#pi-claim" );
            $( ".alert-box" ).html( response.data );
            $('.survey-wrapper').remove();
        }).fail( function ( xhr ) {
            //Let user know it failed and why. Are there other options?
            $( ".required" ).each(function() {
                if ( $(this).val().length > 0 ) {
                    $(this).removeClass('required');
                }
            });
            alert('Please fill all required fields.');
            me.data('requestRunning', false);
        }); 
    }); 

    //Hover effect over images
    if ( $(window).width() >= 1280) 
    {
        $(".pi-image-hover").hover(function () {
            $(this).find( ".pi-image-click" ).stop().animate({
                top: "12%"
            }, 300 );
        }, function () {
            $(this).find( ".pi-image-click" ).stop().animate({
                top: "80%"
            }, 300 );
        });
    }




    //Helper Functions
    function randomnum() {
        var number1 = 1;
        var number2 = 10;
        var randomnum = (parseInt(number2) - parseInt(number1)) + 1;
        var rand1 = Math.floor(Math.random() * randomnum) + parseInt(number1);
        var rand2 = Math.floor(Math.random() * randomnum) + parseInt(number1);
        $(".rand1").html(rand1);
        $(".rand2").html(rand2);
    }  
});