import fs from 'node:fs/promises';
const dir='content-source';
const urls=[...new Set(JSON.parse(await fs.readFile(`${dir}/asset-links.json`,'utf8')).filter(x=>/^https?:\/\/(www\.)?mpcbc\.net\//.test(x)&&/\.pdf(?:$|\?)/i.test(x)).map(x=>x.replace(/^http:/,'https:')))];
await fs.mkdir(`${dir}/documents`,{recursive:true});
const report=[];
for(const url of urls){const name=(url.includes('/english/')?'english-':'main-')+decodeURIComponent(new URL(url).pathname.split('/').pop());try{const r=await fetch(url,{signal:AbortSignal.timeout(45000)});if(!r.ok)throw Error(String(r.status));const buf=Buffer.from(await r.arrayBuffer());if(buf.subarray(0,5).toString()!=='%PDF-')throw Error('not PDF');await fs.writeFile(`${dir}/documents/${name}`,buf);report.push({url,file:name,bytes:buf.length});}catch(e){report.push({url,error:String(e)});}console.log(name);}
await fs.writeFile(`${dir}/documents/report.json`,JSON.stringify(report,null,2));
