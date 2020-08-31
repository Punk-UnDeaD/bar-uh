const style = require('./x-upload-progress.inline.scss')
import {readFileAsDataURL} from "../../helpers/file-reader"
import filesize from 'filesize.js'

customElements.define('x-upload-progress', class extends HTMLElement {
  constructor() {
    super()
    this.attachShadow({mode: 'open'})
    this.shadowRoot.innerHTML = `
    <style>${style}</style>
    <img>
    <div class=mime></div>
    <div class=name></div>
    <div class=progress></div>
    <progress>`
  }

  $(selector) {
    return this.shadowRoot.querySelector(selector)
  }

  set max(max) {
    this.$('progress').max = max;
    if (!max) {
      this.$('progress').removeAttribute('value')
    }
  }

  set progress(progress) {
    const max = this.$('progress').getAttribute('max') * 1;
    if (progress === Infinity) {
      this.$('progress').value = this.$('progress').max = 1;
      this.$('.progress').textContent = 'Complete'
      return
    }
    if (max) {
      this.$('progress').value = progress
      this.$('.progress').textContent = `${filesize(progress)} / ${filesize(max)}`
    }
    if (progress / max >= .99) {
      this.max = 0
    }
  }


  set file(file) {

    if (file instanceof File) {
      const ext = file.name.match(/[\w]+$/)[0]
      const mime = file.type.match(/^[\w]+/)[0]
      this.$('.mime').innerHTML = `<x-icon icon='file file--${mime} file--${ext}' data-ext='${ext}'></x-icon>`
      this.$('.name').textContent = file.name
      this.max = file.size
      this.progress = 0
      if (/image\/.*/.test(file.type)) {
        readFileAsDataURL(file).then(src => this.$('img').src = src)
      }
    }
    else {
      this.$('.name').textContent = file
      this.$('img').src = file
      this.max = 0
    }
  }
})