import {get} from "./send"

let functionHelper = {
	checkIdInArray: function(id, array) {
		let check = false;
		for (var i = 0; i < array.length; i++) {
  			if (array[i].id == id) {
  				check = true;
  				break;
  			}
  		}
  		return check;
	},
	getIndexHasIdInArray: function(id, array) {
		let index = null;
		for (var i = 0; i < array.length; i++) {
  			if (array[i].id == id) {
  				index = i;
  				break;
  			}
  		}
  		return index;
	},
  makePages(path, last_page) {
    let arr = [];
    let index = 1;
    while (index <= last_page) {
      let pus = {
          link: path + '?page=' + index,
          num: index
        }
      arr.push(pus);
      index++;
    }
    return arr;
  }
}

export {functionHelper}