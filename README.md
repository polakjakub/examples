# Příklad vlastního framworku

Zde vidíte kousek minimalistického frameworku, který jsem si kdysi vytvářel. Je 
to kód poněkud starší (a neúplný), ale je celý můj. 

Myšlenka:
- rozdělení do malých bloků (modulů), které dotají XML a to bude transformováno pomocí XSL, cachování XML bloků
- miniální využití PHP pro práci s textem
- jádro - (v ukázce chybí) načte project.yml a podle požadované stránky načte 
    konkrétní moduly ty dodají XML a to je potom transformováno 
- možnost v rámci jádra získat seznam hash podle nastavení modulů (a jejich
    pravidel pro cache) a poté jedním selectem získat XML pro většinu modulů (bloků) a tím mít stránku velmi rychle.
- použití XSL by rovněž umožnilo mít stejné PHP (XML výstupy) i pro mobilní verzi webu.
  