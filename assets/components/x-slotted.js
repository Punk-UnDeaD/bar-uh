customElements.define('x-slotted', class extends HTMLElement {

  constructor() {
    super();
    this.shadow = this.attachShadow({mode: 'open'});
    this.shadow.innerHTML = `
    <style>
:host {
  display: block;}   
    </style>  
    <slot></slot>
    `;
  }
})