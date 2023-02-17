// function sqr(num) {
//     return num ** 2
// }
const sqr = (num) => {
    return num ** 2
}


function checkString(str, char) {
    str = str.toLowerCase()
    char = char.toLowerCase()
    for (var i = 0; i < str.length; i++) {
        if (str[i] == char) {
            return true;
            break;
        }
    }
    return false;
}