import fs from 'node:fs/promises';
const root='.';
for(const locale of ['en','zh-hant','zh-hans']){
 const path=`${root}/elementor/${locale}-about.json`;
 const about=JSON.parse(await fs.readFile(path,'utf8'));
 const beliefs=JSON.parse(await fs.readFile(`${root}/elementor/${locale}-beliefs.json`,'utf8'));
 const section=structuredClone(beliefs.content.find(n=>n.settings?._element_id==='beliefs'));
 if(!section)throw new Error(`Missing reader: ${locale}`);
 if(locale==='en')section.elements[0].elements[0].settings.title='WHAT WE BELIEVE';
 const prior=about.content.findIndex(n=>n.settings?._element_id==='beliefs');
 if(prior>=0)about.content[prior]=section;
 else about.content.splice(-1,1,section); // Replace the old Beliefs teaser with the full native reader.
 const walk=ns=>ns.forEach(n=>{
  const link=n.settings?.link;
  if(link?.url && /\/(?:beliefs)\/?(?:#beliefs)?$/.test(link.url)){
   link.url='#beliefs';
   if(n.settings.title)n.settings.title=locale==='en'?'What we believe ↓':'信仰告白 ↓';
  }
  walk(n.elements||[]);
 });walk(about.content);
 await fs.writeFile(path,JSON.stringify(about,null,2));
}
await fs.writeFile(`${root}/content-source/about-merged-beliefs.json`,JSON.stringify({builder:'scripts/merge-beliefs-about.mjs',apply:'scripts/apply-about-merged-beliefs.php',decision:'Full native Topic reader merged into About#beliefs in all three locales. Old Beliefs URLs redirect.',locales:['en','zh-hant','zh-hans']},null,2));
console.log('Merged reader into three About exports.');
