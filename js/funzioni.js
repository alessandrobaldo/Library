
function msgErrLogout(){
    window.alert("Non è possibile effettuare alcun Logout se prima non effettui il Login");
}

function msgErrLogin(){
    window.alert("Non è possibile effettuare alcun Login se prima non effettui il Logout dalla sessione corrente");
}

function controllaCredenziali(x,y,z){
    //USERNAME

    let reg=/^[a-zA-Z0-9%]{3,6}$/;
    if(!reg.test(x)){
        window.alert("Lo username può contenere solo caratteri alfanumerici o il simbolo % e deve essere di minimo 3 e massimo 6 caratteri");
        return false;
    }

    reg=/^[a-zA-Z%]/;
    if(!reg.test(x)){
        window.alert("Lo username deve iniziare con un carattere alfabetico o il simbolo %");
        return false;
    }
    reg=/[a-zA-Z%]/;
    if(!reg.test(x)){
        window.alert("Lo username deve contenere almeno un carattere non numerico");
        return false;
    }
    reg=/[0-9]/;
    if(!reg.test(x)){
        window.alert("Lo username deve contenere almeno un carattere numerico");
        return false;
    }

    //PASSWORD
    reg=/^[a-zA-Z]{4,8}$/;
    if(!reg.test(y)){
        window.alert("La password può contenere solo caratteri alfabetici e deve essere di minimo 4 e massimo 8 caratteri");
        return false;
    }

    reg=/[a-z]/;
    if(!reg.test(y)){
        window.alert("La password deve contenere almeno un carattere minuscolo");
        return false;
    }

    reg=/[A-Z]/;
    if(!reg.test(y)){
        window.alert("La password deve contenere almeno un carattere maiuscolo");
        return false;
    }

    if(z!=undefined){
        if(z!==y){
            window.alert("Password diversa nel campo 'Conferma Password'");
            return false;
        }
    }

    return true;

}

function resetCredenziali(login){
   login.username.value="";
    login.pwd.value="";
}

function controllaDati(x,y){
    let reg=/\d{1,}$/;
    if(reg.test(x.trim())==false){ //ESSENDO DEI DSTI NON SENSIBILI, ACCETTATI SPAZI CHE ALTRIMENTI CAUSEREBBERO ERRORE
        window.alert("Devi settare un valore numerico per i giorni");
        return false;
    }

    let counter=0;
    for(let i=0; i<y.elements.length; i++){
        if(y.elements[i].type=="checkbox"){
            if(y.elements[i].checked==true)
                counter++;
        }
    }

    if(counter==0){
        window.alert("Devi selezionare almeno un libro");
        return false;
    }


    return true;
}