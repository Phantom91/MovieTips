// server.js
// load the things we need
import { createServer } from "http";
import express from "express";
import minifyHTML from 'express-minify-html';
import * as _ from "underscore";
import moment from 'moment';
import ejs from 'ejs';
import compression from 'compression';

//import translations
import IndexTranslations from './webapp/translations/Index.json';
import LandingPageTranslations from './webapp/translations/LandingPage.json';
import LoginModalTranslations from './webapp/translations/LoginModal.json';
import FooterTranslations from './webapp/translations/Footer.json';

let currentLanguage = 'en',
    translations = {};

let updateTranslations = () => {
    translations = {...IndexTranslations[currentLanguage], ...LandingPageTranslations[currentLanguage], ...FooterTranslations[currentLanguage], ...LoginModalTranslations[currentLanguage]};
}

let compilePartial = (partial, data) => {
    return ejs.render(partial, data);
};

const app = express();

app.set('port', process.env.PORT || 3001);
app.set('trust proxy', true);
// set the view engine to ejs
app.set('view engine', 'ejs');

if(process.env.NODE_ENV == 'production') {
    app.enable('view cache');
    app.use(compression())
}

app.use(express.json({limit: '50mb'}));
//app.use(bodyParser.limit());
app.use(minifyHTML({
    override: true,
    exception_url: false,
    htmlMinifier: {
        removeComments:            true,
        collapseWhitespace:        true,
        collapseBooleanAttributes: true,
        removeAttributeQuotes:     true,
        removeEmptyAttributes:     true,
        minifyJS:                  true
    }
}));

app.locals = {
    moment : moment,
    _ :  _,
    helpers : {
        compilePartial : compilePartial
    }
};

updateTranslations();

createServer(app).listen(app.get('port'), () => {
    console.log("Express server listening on port " + app.get('port'));
});

app.get('/movietips', (req, res) => {
    console.log('Render index');
    res.render(__dirname + '/webapp/templates/index.ejs', {translations : translations});
});

// render templates when request on /renderWithData/{templateId}
app.post('/renderDynamic/:path', (req, res) => {
    console.log('Render dynamic template ' + req.params.path + '.ejs');
    res.render(__dirname + '/webapp/templates/' + req.params.path + '.ejs', req.body);
});

// render templates when request on /renderWithData/{templateId}
app.post('/renderDynamic/:path1/:path2', (req, res) => {
    console.log('Render dynamic template ' + req.params.path1 + '/' + req.params.path2 + '.ejs');
    res.render(__dirname + '/webapp/templates/' + req.params.path1 + '/' + req.params.path2 + '.ejs', req.body);
});

// render templates when request on /render/{folder}/{templateId}
app.get('/renderStatic/:path', (req, res) => {
    console.log('Render static template ' + req.params.path + '.ejs');
    res.render(__dirname + '/webapp/templates/' + req.params.path + '.ejs', {translations : translations});
});

// render templates when request on /render/{folder}/{templateId}
app.get('/renderStatic/:path1/:path2', (req, res) => {
    console.log('Render static template ' + req.params.path1 + '/' + req.params.path2 + '.ejs');
    res.render(__dirname + '/webapp/templates/' + req.params.path1 + '/' + req.params.path2 + '.ejs', {translations : translations});
});