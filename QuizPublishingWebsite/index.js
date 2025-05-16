function inpDesign(id) {
    let inputID = document.getElementById(id);
    inputID.style.border = "2px solid blue";
    inputID.style.boxShadow = "none";
    inputID.style.outline = "none";
    inputID.style.backgroundColor = "#e0f7fa";
}

function inpRevert(id) {
    let inputID = document.getElementById(id);
    inputID.style.border = "2px solid black";
    inputID.style.backgroundColor = "white";  
}

function hidePW() {
    var id = document.getElementById("pw");
    let pwShow = document.getElementById("pwVisible");
    let pwHide = document.getElementById("pwNotVisible");
    if (id.type === "password") {
    id.type = "text";
    pwHide.style.display = "none";
    pwShow.style.display = "block";
    } else {
    id.type = "password";
    pwHide.style.display = "block";
    pwShow.style.display = "none";
    }
  }


function vadForm(event) {
    event.preventDefault();
    us = document.getElementById("us").value;
    pw = document.getElementById("pw").value;
    bError = document.getElementById("blankError");
    iError = document.getElementById("invalidError");
    
    //check null and empty fields
    bError.style.display = "block";
    if (!us && !pw || us.length == 0 && pw.length == 0)
        bError.innerHTML = "Username and Password field is not filled.";
    else if (!us || us.length == 0)
        bError.innerHTML = "Username field is not filled.";
    else if (!pw || pw.length == 0)
        bError.innerHTML = "Password field is not filled.";
    else
        bError.style.display = "none";
    
    //check field length
    if (us.length > 0 && pw.length > 0) {
        if (us.length >= 4 && pw.length >= 8) {
            iError.style.display = "none";
            console.log("hello");
            event.target.submit();
        } else {
            iError.style.display = "block";
            if (us.length < 4 && pw.length < 8)
                iError.innerHTML = "Username and Password are invalid.";
            else if (us.length < 4)
                iError.innerHTML = "Username is invalid.";
            else if (pw.length < 8)
                iError.innerHTML = "Password is invalid.";
        }
    } else 
        iError.style.display = "none";
}  