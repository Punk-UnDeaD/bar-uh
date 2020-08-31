class Xhr {
    private readonly xhr: XMLHttpRequest;
    private readonly url: string;
    private readonly options: any;

    constructor(url?: string, options?: object) {
        this.url = url || window.location.href
        this.options = Object.assign({
            method: 'GET',
            headers: {}
        }, options || {})
        this.xhr = new XMLHttpRequest()
    }

    addHeader(header: string, value: string) {
        this.options.headers[header] || (this.options.headers[header] = [])
        if (!(this.options.headers[header] instanceof Array)) {
            this.options.headers[header] = [this.options.headers[header]]
        }
        this.options.headers[header].push(value)
        return this
    }

    on(event: string, callback: any) {
        event.split(' ').forEach((e: string) => {
            if ('upload.' === e.substring(0, 7)) {
                this.xhr.upload.addEventListener(e.substring(7), callback);
            }
            this.xhr.addEventListener(e, callback);
        })
        return this
    }

    send() {
        this.xhr.open(this.options.method, this.url)
        for (const dataKey in this.options.headers) {
            if (this.options.headers.hasOwnProperty(dataKey))
                if (this.options.headers[dataKey] instanceof Array) {
                    this.xhr.setRequestHeader(dataKey, this.options.headers[dataKey].join(', '))
                } else {
                    this.xhr.setRequestHeader(dataKey, this.options.headers[dataKey])
                }
        }
        this.xhr.send(this.options.body)
        return this
    }

    promise() {
        return new Promise((resolve, reject) => {
            this.on('load', () => {
                const headers = this.xhr.getAllResponseHeaders().split('\n').filter((e: any) => !!e).reduce((headers: any, s: string) => {
                    const header = /(.+?): (.+)/.exec(s);
                    if (header) {
                        headers[header[1]] = header[2];
                    }
                    return headers
                }, {});
                const resp = new Response(this.xhr.response, {
                    status: this.xhr.status,
                    statusText: this.xhr.statusText,
                    headers
                })
                resolve(resp)
            })
            this.on('abort timeout error', (e: any) => reject(e))
        })
    }
}

export function xfetch() {
    return new Xhr(...arguments)
}
