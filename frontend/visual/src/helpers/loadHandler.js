import axios from 'axios';
import {getSvgSizes} from './svgHelper';

export default (url) => {
    return new Promise((resolve, reject) => {
        if (url.indexOf('.svg') > -1) {
            axios.get(url, {responseType: 'blob'})
                .then((response) => {
                    getSvgSizes(response.data, resolve, reject);
                });

            return;
        }

        const img = new Image();

        img.src = url;
        img.onload = (event) => {
            resolve({
                width : event.currentTarget.width,
                height: event.currentTarget.height
            });
        };
        img.onerror = (error) => {
            reject(error);
        };
    });
};
