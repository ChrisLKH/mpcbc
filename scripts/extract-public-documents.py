from pathlib import Path
from pypdf import PdfReader
import json
root=Path('content-source/documents')
report=[]
for file in sorted(root.glob('*.pdf')):
    try:
        reader=PdfReader(file)
        texts=[page.extract_text() or '' for page in reader.pages]
        file.with_suffix('.txt').write_text('\n\n'.join(f'PAGE {i+1}\n{text}' for i,text in enumerate(texts)),encoding='utf-8')
        report.append({'file':file.name,'pages':len(texts),'characters':sum(map(len,texts)),'needs_ocr':any(len(t.strip())<40 for t in texts)})
    except Exception as e:
        report.append({'file':file.name,'error':str(e)})
(root/'extraction-report.json').write_text(json.dumps(report,ensure_ascii=False,indent=2),encoding='utf-8')
print(json.dumps({'documents':len(report),'pages':sum(r.get('pages',0) for r in report),'needs_ocr':[r['file'] for r in report if r.get('needs_ocr')],'errors':[r for r in report if 'error' in r]}))
