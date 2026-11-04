from pathlib import Path
from html.parser import HTMLParser
import json
class Text(HTMLParser):
    def __init__(self):super().__init__();self.parts=[];self.skip=0
    def handle_starttag(self,tag,attrs):
        if tag in ('script','style'):self.skip+=1
        if tag in ('p','div','li','h1','h2','h3','h4','br'):self.parts.append('\n')
    def handle_endtag(self,tag):
        if tag in ('script','style'):self.skip=max(0,self.skip-1)
    def handle_data(self,data):
        if not self.skip:self.parts.append(data)
root=Path('content-source')
for file in root.glob('*.html'):
    p=Text();p.feed(file.read_text(encoding='utf-8'))
    text='\n'.join(line.strip() for line in ''.join(p.parts).splitlines() if line.strip())
    file.with_suffix('.txt').write_text(text,encoding='utf-8')
print('Indexed rendered HTML text.')
