function fw_required_input_handler(){
  var items = this.form.querySelectorAll("[name='"+this.name+"']"), 
      someChecked = this.form.querySelectorAll("[name='"+this.name+"'][checked]").length>0;
  for (var i=0, l=items.length; i<l; i++) {
    if (someChecked) {
      items[i].removeAttribute("required")
    } else {
      items[i].setAttribute("required","required")
    } 
  }
}