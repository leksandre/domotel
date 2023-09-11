export const getSvgSizes = (fileData, resolve, reject) => {
    const reader = new FileReader();

    reader.onload = () => {
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
    reader.readAsText(fileData);
};
