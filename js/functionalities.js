jQuery('#cep').mask('00000-000');
jQuery('#billing_postcode-2612').mask('00000-000');
jQuery('#cpf').mask('000.000.000-00');
jQuery('#cpf_user').mask('000.000.000-00');
jQuery('#cpf_user-2612').mask('000.000.000-00');
jQuery('#data').mask('00/00/0000');
jQuery('#nasc_user').mask('00/00/0000');
jQuery('#nasc_user-2612').mask('00/00/0000');
jQuery('.cel').mask('(00) 00000-0000');
jQuery('#billing_phone-2612').mask('(00) 00000-0000');
jQuery('#mobile_number-2612').mask('(00) 00000-0000');

jQuery("#cep").change(function() {

    var cep = jQuery(this).val().replace("-", "");
    var count = jQuery(this).val().replace("-", "").length;

    if (count == 8) {
        jQuery.getJSON("https://viacep.com.br/ws/" + cep + "/json/")
            .done(function(json) {

                //console.log(json);

                jQuery('#end').val(json.logradouro);
                jQuery('#bairro').val(json.bairro);
                jQuery('#cidade').val(json.localidade);
                jQuery('#estado').val(json.uf);
            })
            .fail(function(jqxhr, textStatus, error) {
                var err = textStatus + ", " + error;
                console.log("Request Failed: " + err);
            });
    }
});

jQuery("#billing_postcode-2612").change(function() {

    var cep = jQuery(this).val().replace("-", "");
    var count = jQuery(this).val().replace("-", "").length;

    if (count == 8) {
        jQuery.getJSON("https://viacep.com.br/ws/" + cep + "/json/")
            .done(function(json) {

                //console.log(json);

                jQuery('#billing_address_1-2612').val(json.logradouro);
                jQuery('#billing_neighborhood-2612').val(json.bairro);
                jQuery('#billing_city-2612').val(json.localidade);
                jQuery('#billing_state-2612').val(json.uf);
            })
            .fail(function(jqxhr, textStatus, error) {
                var err = textStatus + ", " + error;
                console.log("Request Failed: " + err);
            });
    }
});