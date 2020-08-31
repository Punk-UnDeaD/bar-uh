import {xfetch} from '../helpers/xhr'

import './x-upload-progress/x-upload-progress'

customElements.define('x-file-drop', class extends HTMLElement {
  on(events, callback) {
    events.split(' ').forEach(e => this.addEventListener(e, callback))
    return this
  }

  connectedCallback() {
    this
        .on('drag dragstart dragend dragover dragenter dragleave drop', e => {
          e.preventDefault()
          e.stopPropagation()
        })
        .on('dragenter', e => {
          this.classList.add('ready')
          this.classList.remove('done')
        })
        .on('dragleave drop', e => {
          this.classList.remove('ready')
        })
        .on('drop', e => {
          const files = [...e.dataTransfer.files]
          if (!files.length) {
            return
          }
          this.classList.add('active')

          this.uploadFiles(files)
        })

    this.counter = 0

    const input = this.querySelector('input[type=file]')
    if (input) {
      input.addEventListener('change', e => {
        if (input.files.length) {
          [...input.files].forEach(f => this.uploadFile(f))
          input.value = ''
        }
      })
    }
    if (this.hasAttribute('clipboard')) {
      window.addEventListener('paste', (e) => this.onPaste(e))
    }
  }

  get counter() {
    return this._counter * 1
  }

  set counter(val) {
    this._counter = val
    this.style.setProperty('--counter', val)
  }

  get for() {
    return this.getAttribute('for')
  }

  uploadIndicator(file) {
    const upload_progress = document.createElement('x-upload-progress')
    upload_progress.file = file
    this.appendChild(upload_progress)

    return upload_progress
  }

  uploadFile(file) {
    const formData = new FormData()

    const input_name = file instanceof File ? 'file' : 'url';
    formData.append(input_name, file)
    ++this.counter

    const indicator = this.uploadIndicator(file)

    xfetch(this.dataset.url || '', {
      method: 'POST',
      body: formData,
      headers: {
        accept: 'application/json',
      }
    })
        .on('upload.progress', (e) => {
          indicator.progress = e.loaded
        })
        .send()
        .promise()
        .then(r => r.json())
        .then(data => {
          indicator.progress = Infinity
          if (!(--this.counter)) {
            this.classList.add('done')
            this.classList.remove('active')
          }
        })
  }

  uploadFiles(files) {
    files.forEach(file => this.uploadFile(file))
  }

  onPaste(event) {
    const items = (event.clipboardData || event.originalEvent.clipboardData).items
    for (let index in items) {
      if (!items.hasOwnProperty(index)) {
        continue
      }
      let item = items[index];
      if (item.kind === 'file') {
        let file = item.getAsFile();
        this.uploadFile(file)
      }
      else if (item.kind === 'string') {
        item.getAsString(s => {
              if (/^https?:\/\//.exec(s)) {
                this.uploadFile(s)
              }
            }
        );
      }
    }
  }
})
