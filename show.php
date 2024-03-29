<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<title>Perseus</title>

<link rel="stylesheet" type="text/css" href="ke/css/khan-site.css" />
<link rel="stylesheet" type="text/css" href="ke/css/khan-exercise.css" />
<link rel="stylesheet" type="text/css" href="lib/katex/fonts/fonts.css" />
<link rel="stylesheet/less" type="text/css" href="lib/katex/katex.less" />
<link rel="stylesheet" type="text/css" href="lib/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="lib/mathquill/mathquill.css" />

<link rel="stylesheet/less" type="text/css" href="stylesheets/exercise-content-package/perseus.less" />
<link rel="stylesheet/less" type="text/css" href="stylesheets/perseus-admin-package/editor.less" />

<script>less = {env: 'development', logLevel: 1};</script>
<script src="lib/less.js"></script>
<style type="text/css">
.perseus-editor-left-cell {
    /*display: none;*/
}
</style>
</head>
<body>

<div id="extras">
    <button id="serialize">serialize</button>
    <button id="scorePreview">Score</button>
    <button id="permalink">permalink</button>
    <span>Seed:</span><span id="problemNum"></span>
    <span>Features:</span><span id="enabledFeatures"></span>
</div>

<!-- Begin Perseus HTML -->
<div id="perseus-container">
</div>
<!-- End Perseus HTML -->

<!-- put an empty div here so the margin on the perseus editor has something
to "push against" (without the div, the padding goes off the page, and the
add hint button ends up touching the bottom of the page). -->
<div class="clear"></div>

<script src="lib/jquery.js"></script>
<script src="lib/underscore.js"></script>
<script src="lib/marked.js"></script>
<script src="lib/react-with-addons.js"></script>
<script src="ke/third_party/MathJax/2.1/MathJax.js?config=KAthJax-8f02f65cba7722b3e529bd9dfa6ac25d&amp;delayStartupUntil=configured"></script>
<script src="lib/katex/katex.js"></script>
<script src="lib/mathquill/mathquill-basic.js"></script>
<script src="lib/kas.js"></script>

<script>
    var icu = {
        getDecimalFormatSymbols: function() {
            return {
                decimal_separator: ".",
                grouping_separator: ",",
                minus: "-"
            };
        }
    };
    var KhanUtil = {
        debugLog: function() {},
        localeToFixed: function(num, precision) {
            return num.toFixed(precision);
        }
    };
    var Khan = {
        Util: KhanUtil,
        error: function() {},
        query: {debug: ""},
        imageBase: "/ke/images/",
        scratchpad: {
            enable: function() {},
            disable: function() {}
        }
    };
    React.initializeTouchEvents(true);
</script>

<script src="ke/local-only/jed.js"></script>
<script src="ke/local-only/i18n.js"></script>
<script src="ke/local-only/jquery.qtip.js"></script>
<script src="ke/exercises-stub.js"></script>
<script src="ke/local-only/require.js"></script>

<script>
(function() {

requirejs.config({
    waitSeconds: 120
});

// Load khan-exercises modules, then perseus
require(["ke-deps.js"], function() {
    // pre built
    // require(["build/perseus.js"], initPerseus);

    // pre built with source maps
    // require(["build/perseus.debug.js"], initPerseus);

    // built on demand
    require(["live-build/perseus.js"], initPerseus);
});

function initPerseus(Perseus) {

window.Perseus = Perseus;

var defaultQuestion = {
    "question": {
        "content": "[[☃ interactive-graph 2]]",
        "images": {
            "https://ka-perseus-graphie.s3.amazonaws.com/da8df81c78b22f5c69d477d8eabfb583968eaf84.png": {
                "width": 400,
                "height": 70
            },
            "https://ka-perseus-graphie.s3.amazonaws.com/b59fc02ca1aae800977b8793ed22f647a1aa75ee.png": {
                "width": 425,
                "height": 150
            }
        },
        "widgets": {
            "interactive-graph 2": {
                "type": "interactive-graph",
                "graded": true,
                "options": {
                    "step": [
                        2,
                        2
                    ],
                    "backgroundImage": {
                        "url": null,
                        "scale": 1,
                        "bottom": 0,
                        "left": 0
                    },
                    "markings": "graph",
                    "labels": [
                        "x",
                        "y"
                    ],
                    "showProtractor": false,
                    "showRuler": false,
                    "rulerLabel": "",
                    "rulerTicks": 10,
                    "range": [
                        [
                            -20,
                            20
                        ],
                        [
                            -20,
                            20
                        ]
                    ],
                    "graph": {
                        "type": "linear"
                    },
                    "correct": {
                        "type": "linear",
                        "coords": [
                            [
                                0,
                                0
                            ],
                            [
                                5,
                                5
                            ]
                        ]
                    }
                },
                "version": {
                    "major": 0,
                    "minor": 0
                }
            }
        }
    },
    "answerArea": {
        "type": "multiple",
        "options": {
            "content": "",
            "images": {},
            "widgets": {}
        },
        "calculator": false
    },
    "itemDataVersion": {
        "major": 0,
        "minor": 1
    },
    "hints": []
};

var editor;
var problemNum = _.random(1, 99);
var enabledFeatures = {
    highlight: true,
    toolTipFormats: true,
    useMathQuill: true
};

$('#serialize').on('click', function() {
    console.log(JSON.stringify(editor.serialize(), null, 4));
});
$('#scorePreview').on('click', function() {
    console.log(editor.scorePreview());
});
$('#permalink').on('click', function(e) {
    window.location.hash = "content=" +
        Perseus.Util.strongEncodeURIComponent(JSON.stringify(editor.serialize()));
    e.preventDefault();
});
$('#problemNum').text(problemNum);
$('#enabledFeatures').html(_.map(enabledFeatures, function(enabled, feature) {
    return '<span style="margin-left: 5px; background: ' +
            (enabled ? "#aaffaa" : "#ffcccc") + ';">' + feature + '</span>';
}).join(''));

var query = Perseus.Util.parseQueryString(window.location.hash.substring(1));
var question = query.content ? JSON.parse(query.content) : defaultQuestion;

Perseus.init({skipMathJax: false}).then(function() {

    var editorProps = _.extend(question, {
        problemNum: problemNum,
        enabledFeatures: enabledFeatures,
        developerMode: true,
        imageUploader: function(image, callback) {
            _.delay(callback, 1000, "http://fake.image.url");
        },
        apiOptions: {
            fancyDropdowns: true,
            __onInputError: function() {
                var args = _.toArray(arguments);
                console.log.apply(console, ["onInputError:"].concat(args));
                return true;
            },
            __interceptInputFocus: function() {
                var args = _.toArray(arguments);
                console.log.apply(console, ["interceptInputFocus:"].concat(args));
                return;
            },
            onFocusChange: function(newPath, oldPath) {
                console.log("onFocusChange", newPath, oldPath);
            },
            __staticRender: true
        }
    });

    editor = React.renderComponent(
        Perseus.StatefulEditorPage(editorProps, null),
        document.getElementById("perseus-container")
    );

    // Some hacks to make debugging nicer
    window.editorPage = editor.refs.editor;
    window.itemRenderer = window.editorPage.renderer;
}).then(function() {
}, function(err) {
    console.error(err);
});

}

})();
</script>

</body>
</html>
