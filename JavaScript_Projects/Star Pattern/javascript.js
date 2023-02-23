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
      if (
        row == 1 ||
        row == 3 ||
        row == 5 ||
        (col == 1 && row == 2) ||
        (row == 4 && col == 4)
      ) {
        document.write("*");
      } else {
        document.write("&nbsp &nbsp");
      }
    }
    // Provide Space Btw Pattern
    for (space = 1; space <= 3; space++) {
      document.write("&nbsp");
    }

    document.write("</br>");
  }
  document.write("</br>");
  //Create  Star Pattern for  Chracter "A"
  let row1, col1;
  for (row1 = 1; row1 <= x; row1++) {
    for (col1 = 1; col1 <= x; col1++) {
      if (row1 == 1 || col1 == 1 || row1 == 3 || col1 == 5) {
        document.write("*");
      } else {
        document.write("&nbsp ");
      }
    }
    document.write("</br>");
  }
  document.write("</br>");
  //Create  Star Pattern for  Chracter "U"
  let row2, col2;
  for (row2 = 1; row2 <= x; row2++) {
    for (col2 = 1; col2 <= x; col2++) {
      if (row2 == 5 || col2 == 1 || col2 == 5) {
        document.write("*");
      } else {
        document.write("&nbsp ");
      }
    }
    document.write("</br>");
  }
  document.write("</br>");
  //Create  Star Pattern for  Chracter "R"
  let row3, col3;
  for (row3 = 1; row3 <= x; row3++) {
    for (col3 = 1; col3 <= x; col3++) {
      if (
        row3 == 1 ||
        col3 == 1 ||
        row3 == 3 ||
        (row3 == 2 && col3 == 5) ||
        (row3 == 4 && col3 == 3) ||
        (row3 == 5 && col3 == 5)
      ) {
        document.write("*");
      } else {
        document.write("&nbsp ");
      }
    }
    document.write("</br>");
  }
  document.write("</br>");
  //Create  Star Pattern for  Chracter "A"
  let row4, col4;
  for (row4 = 1; row4 <= x; row4++) {
    for (col4 = 1; col4 <= x; col4++) {
      if (row4 == 1 || col4 == 1 || row4 == 3 || col4 == 5) {
        document.write("*");
      } else {
        document.write("&nbsp ");
      }
    }
    document.write("</br>");
  }
  document.write("</br>");
  //Create  Star Pattern for  Chracter "V"
  let row5, col5;
  x1 = 5;
  x2 = 5;
  for (row5 = 1; row5 <= x2; row5++) {
    for (col5 = 1; col5 <= x1; col5++) {
      if (
        (row5 == 1 && col5 == 1) ||
        (row5 == 1 && col5 == 5) ||
        (row5 == 2 && col5 == 2) ||
        (row5 == 2 && col5 == 4) ||
        (row5 == 3 && col5 == 3) 
      ) {
        document.write("*");
      } else {
        document.write("&nbsp ");
      }
    }
    document.write("</br>");
  }
}
writename();
// starpaternsimple();
// starpaternright();
