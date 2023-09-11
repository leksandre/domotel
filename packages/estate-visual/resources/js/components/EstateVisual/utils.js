export const readFile = (file) => {
    return new Promise((resolve, reject) => {
        if (!(file instanceof File)) {
            reject('Invalid argument type');
        }

        const reader = new FileReader();

        reader.onerror = () => {
            reject(reader.error);
        };
        reader.onloadend = () => {
            resolve(reader.result);
        };

        reader.readAsText(file);
    });
};

export const uploadFile = (file, storageUrl, storage, groups) => {
    return new Promise((resolve, reject) => {
        if (!(file instanceof File)) {
            reject('Invalid argument type');
        }

        const formData = new FormData();

        formData.append('file', file);
        formData.append('storage', storage);
        formData.append('group', groups);

        window.axios.post(storageUrl, formData)
            .then((response) => {
                resolve(response.data);
            })
            .catch((error) => {
                reject(error);
            });
    });
};

export const getMasksFromSvg = (svgData) => {
    const doc = new DOMParser().parseFromString(svgData, 'image/svg+xml');
    const paths = doc.getElementsByTagName('path');
    const masks = [];

    if (!paths.length) {
        return masks;
    }

    for (const path of paths) {
        const attrData = path.getAttribute('d');

        if (attrData) {
            masks.push({
                serialNumber: path.getAttribute('id'),
                coords: attrData
            });
        }
    }

    return masks;
};

export const getMasksFromString = (str) => {
    const res = [];
    const matches = str.matchAll(/(m([achlqstv\d.,|\- ]+)z)/gim);

    for (const match of matches) {
        if (typeof match[1] === 'string') {
            res.push(match[1]);
        }
    }

    return res;
};

export const getImageSizes = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        const getSvgSizes = (file, resolve, reject) => {
            reader.onloadend = () => {
                const searchString = 'viewBox="';
                const viewBoxStartPos = reader.result.indexOf(searchString);

                if (viewBoxStartPos === -1) {
                    reject('viewBox not found at SVG file');
                }

                const viewBoxValue = reader.result.slice(viewBoxStartPos + searchString.length,
                    reader.result.indexOf('"', viewBoxStartPos + searchString.length));

                let width = 0;
                let height = 0;

                [, , width, height] = viewBoxValue.trim().split(' ');

                if (!width && !height) {
                    reject('Width and Height of SVG is undefined');
                }

                resolve({
                    width,
                    height
                });
            };
            reader.onerror = () => {
                reject(reader.error);
            };
            reader.readAsText(file);
        };

        reader.onloadend = () => {
            let img = new Image();

            img.src = reader.result;
            img.onload = () => {
                if (!img.width && !img.height && img.src.indexOf('data:image/svg+xml') > -1) {
                    img = null;
                    getSvgSizes(file, resolve, reject);

                    return;
                }
                resolve({
                    width : img.width,
                    height: img.height
                });
            };
        };
        reader.onerror = () => {
            reject(reader.error);
        };
        reader.readAsDataURL(file);
    });
};

export const getUniqueKey = () => {
    if (typeof crypto.randomUUID === 'function') {
        return crypto.randomUUID();
    }

    const tmpUrl = URL.createObjectURL(new Blob());
    const uuid = tmpUrl.toString();

    URL.revokeObjectURL(tmpUrl);

    return uuid.split('/').pop().toLowerCase();
};

export const fileName = (path) => {
    return path ? path.split('/').pop() : null;
};

export const fileExtension = (filename) => {
    return filename.split('.').pop().toUpperCase() || null;
};
