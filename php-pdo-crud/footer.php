</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9b1MRP1ylwQ0lI1j6V+1nEw5xZ7r5KkN1NVr7s6I" crossorigin="anonymous"></script>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- jQuery Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    $(document).ready(function() {
        // Máscara de telefone
        $('#telefone').mask('(00) 00000-0000');

        // Campo de data de nascimento
        if (!$('#data_nascimento').val()) {
            $('#data_nascimento').attr('type', 'date');
        }
    });

    // Funções ViaCEP
    function limpa_formulario_cep() {
        $("#logradouro").val("");
        $("#bairro").val("");
        $("#cidade").val("");
        $("#uf").val("");
    }

    function pesquisacep(valor) {
        var cep = valor.replace(/\D/g, '');
        if (cep !== "") {
            var validacep = /^[0-9]{8}$/;
            if (validacep.test(cep)) {
                $("#logradouro").val("...");
                $("#bairro").val("...");
                $("#cidade").val("...");
                $("#uf").val("...");
                $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {
                    if (!("erro" in dados)) {
                        $("#logradouro").val(dados.logradouro);
                        $("#bairro").val(dados.bairro);
                        $("#cidade").val(dados.localidade);
                        $("#uf").val(dados.uf);
                    } else {
                        limpa_formulario_cep();
                        alert("CEP não encontrado.");
                    }
                });
            } else {
                limpa_formulario_cep();
                alert("Formato de CEP inválido.");
            }
        } else {
            limpa_formulario_cep();
        }
    }
</script>
</body>

</html>