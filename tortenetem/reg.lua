local file = io.open("index_old.html", "r")
local str = file:read("*a")


function trim(s)
    if not s then return nil end
    return s:match "^%s*(.-)%s*$"
end

local sub = {}




for w in str:gmatch([[<article class="pique%-panel">.-</article>]]) do table.insert(sub,trim(w)) end



print(
[[
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="UTF-8">

        <title>V&aacute;raljai P&eacute;ter</title>

        <link rel="stylesheet" href="../main/css/style.css" type="text/css" media="screen">
    </head>

    <body style="background-color: #000000">

        <header id="masthead" style="height: 438px;">

<div class="article"><h2 class="entry-title"><pre><code>

|__   ___|/ /_/     | |  /_/           | |              
    | |  ___  _ __| |_ ___ _ __   ___| |_ ___ _ __ ___  
    | | / _ \| '__| __/ _ \ '_ \ / _ \ __/ _ \ '_ ` _ \ 
    | |  (_) | |  | ||  __/ | | |  __/ ||  __/ | | | | |
    |_| \___/|_|   \__\___|_| |_|\___|\__\___|_| |_| |_|
                                                        
</code></pre></h2></div>
            
        
        </header>
]])


for i,v in ipairs(sub) do
    local img = trim(v:match("img/(.-)[)]"))
    local tit = trim(v:match([[entry%-title">(.-)<]]))
    local text = trim(v:match([[entry%-content">(.-)</div>]]))

    print('\t\t<div class="fix-background article" style="background-image:url(img/'..img..')">')
    if tit then
        print('            <h2 class="entry-title">')
        print(tit)
        print('</h2>')
    end
    print('\t\t\t<div class="entry-content">')
    print(text)
    print("\t\t\t</div>\n\t\t</div>")
end

print(
[[


        <footer class="site-footer">
            <p>V&aacute;raljai P&eacute;ter</p>
        </footer>

    </body>

</html>
]])