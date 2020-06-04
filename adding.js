function addingPos(){
  countPos = 0;

  $('#addpos').click(function(event){

    event.preventDefault();
    if (countPos>=9) {alert("Maximum of nine positions entries exceeded");
    return;}
   
   countPos++;
   console.log("Adding position "+ countPos);
   $('#position_fields').append(
      '<div id="position'+countPos+'" > \
      <p> Year: <input type= "text" name = "year'+countPos+'"/> \
      <input type= "button" value = "-" onclick = "$(#position'+countPos+').remove();return false;" \
      <textarea name ="desc'+countPos+'" rows = "8" cols="80"></textarea> \
      </div>) '

      
    )


  })
}


$(#position'+countPos+').remove();return false;