function starpaternsimple() {
  for (let i = 1; i <= 5; i++) {
    for (let j = 1; j <= i; j++) {
      document.write("*");
    }
    document.write("<br/>");
  }
}
function starpaternright() {
  var x = 5;
  for (let i = 1; i <= x; i++) {
    // first loop for rows

    for (let k = 1; k <= 5 - i; k++) {
      //second loop for empty space
      document.write("&nbsp;&nbsp;"); // &nbsp; is HTML Entity to use for empty space. we are use two time &nbsp; because 1 character(size) = 2 space(size)
    }
    for (let j = 1; j <= i; j++) {
      // third loop for columns
      document.write("*"); // print *
    }
    document.write("<br/>"); //print line break
  }
}
function writename() {
  let row,
    col,
    x = 5;
    // space = 1;
  //Create  Star Pattern for  Chracter "S"
  for (row = 1; row <= x; row++) {
    for (col = 1; col <= x; col++) {
      if( (row == 1 || (row ==2 && col == 1) || row ==3 || ( row == 4 && col == 3) || row == 5 )){
        document.write("*");
      }else{
        document.write("&emsp;");
      }
    }
    //Provide Space Btw Pattern
    for (space = 1; space <= 3; space++) {
      document.write("&emsp;");
    }
    //Create  Star Pattern for  Chracter "A"
    for (col = 1; col <= x; col++) {
      if(col==1 || col==5 || row ==1 || row ==3){
        document.write("*");
      }else{
        document.write(" ");
      }
    }
    document.write("</br>");
  }
}
writename();
// starpaternsimple();
// starpaternright();
