<html>

<head>
<script src="../jwplayer.js"></script>
  <script type='text/javascript'>jwplayer.key = "XtWFuCK/ytTi7a/eN1uvsRtq6MpIHJTm1hiLbQ==";</script>


jwplayer("mediaplayer").setup({
                primary: "flash",
                playlist: [{
                    file: videourl,
                    tracks: [{
                        file: trickplayurl,
                        kind: "thumbnails"
                    }]
                }],
                width: 550,
                height: 315
            });
</head>

<body>
<div id="mediapalyer"></div>
</body>
</html>