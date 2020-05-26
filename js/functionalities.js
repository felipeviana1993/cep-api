jQuery('#cep').mask('00000-000');
jQuery('#cpf').mask('000.000.000-00');
jQuery('.cel').mask('(00) 00000-0000');

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