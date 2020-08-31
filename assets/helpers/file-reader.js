export function readFileAsDataURL(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onloadend = () => {
      resolve(reader.result)
    }
    reader.onerror = reader.onabort = (e) => {
      reject(e)
    }
  });
}