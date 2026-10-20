"""
State FAQ answers, English and Spanish, one entry per verified fact.

Facts come from official state sources (election office or statute), checked October 19, 2026; the
research with source quotes is in docs/faq-sources.json. Keys: polls (Election Day hours), mail (ballot
deadline in all-mail states), id, reg, selfie. A missing key means no official source answered it, and
the page leaves that question out.

Writing rules: short plain sentences, answer first (Yes, No, Usually not), no advice beyond the source,
no em dashes, keep "may" and "can" conditional. Spanish follows docs/spanish-style.md (tú, glossary,
English state names, official titles in English in brackets) and the es-mx-localization skill.

    python3 -I build/faq_text.py    # writes site/faq-states.php and docs/faq-sources.json
"""
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent

A = {
    'AL': {
        'polls': ("Polls are open 7 a.m. to 7 p.m. Some polling places in the Eastern Time Zone keep those hours in Eastern Time.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. Algunos lugares de votación en la zona horaria del Este usan ese horario en hora del Este."),
        'id': ("Yes. Bring a photo ID, such as an Alabama driver's license, a U.S. passport, or a free Alabama Photo Voter ID card. Without one, you can vote a regular ballot if two poll workers can identify you.",
               "Sí. Lleva una identificación con foto, como una licencia de conducir de Alabama, un pasaporte de EE. UU. o la credencial gratuita Alabama Photo Voter ID. Si no tienes una, puedes votar con una boleta regular si dos trabajadores electorales te identifican."),
        'reg': ("Only through October 19, 2026, online, by mail, or in person. Alabama has no Election Day registration.",
                "Solo hasta el 19 de octubre de 2026, en línea, por correo o en persona. Alabama no tiene registro el Día de las Elecciones."),
        'selfie': ("Yes, you can take a photo of your own ballot. Other photos inside the polling place are not allowed.",
                   "Sí, puedes tomarle foto a tu propia boleta. No se permiten otras fotos dentro del lugar de votación."),
    },
    'AK': {
        'polls': ("Polls are open 7 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m."),
        'id': ("Yes. Show one ID, such as your voter card, a driver's license, or a passport. A current utility bill or bank statement with your name and address also counts.",
               "Sí. Muestra una identificación, como tu tarjeta de votante, una licencia de conducir o un pasaporte. También sirve un recibo de servicios o un estado de cuenta bancario reciente con tu nombre y dirección."),
        'reg': ("No, not for the November 3 election. The deadline was October 4, 2026, and Alaska has no Election Day registration for state and federal races.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 4 de octubre de 2026, y Alaska no tiene registro el Día de las Elecciones para las elecciones estatales y federales."),
        'selfie': ("No. Alaska law bars showing your marked ballot to anyone in a way that reveals how you voted, and a photo of it would do that.",
                   "No. La ley de Alaska prohíbe mostrar tu boleta marcada a cualquier persona de una forma que revele cómo votaste, y una foto de la boleta lo haría."),
    },
    'AZ': {
        'polls': ("Polls are open 6 a.m. to 7 p.m.",
                  "Los lugares de votación abren de 6 a. m. a 7 p. m."),
        'id': ("Yes. Show one photo ID with your name and address, such as an Arizona driver license or a tribal ID. Without one, two documents such as a utility bill and a bank statement can work.",
               "Sí. Muestra una identificación con foto que tenga tu nombre y dirección, como una licencia de conducir de Arizona o una identificación tribal. Si no tienes una, pueden servir dos documentos, como un recibo de servicios y un estado de cuenta bancario."),
        'reg': ("No, not for the November 3 election. The deadline was October 5, 2026, and Arizona has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 5 de octubre de 2026, y Arizona no tiene registro el Día de las Elecciones."),
        'selfie': ("Yes, outside the polling place. Arizona allows posting a photo of your own ballot, but no one may take photos or video within 75 feet of a polling place.",
                   "Sí, fuera del lugar de votación. Arizona permite publicar una foto de tu propia boleta, pero nadie puede tomar fotos ni video a menos de 75 pies de un lugar de votación."),
    },
    'AR': {
        'polls': ("Polls are open 7:30 a.m. to 7:30 p.m.",
                  "Los lugares de votación abren de 7:30 a. m. a 7:30 p. m."),
        'id': ("Yes. Show a photo ID, such as an Arkansas driver's license, a U.S. passport, or a concealed carry license. Without one, you vote a provisional ballot and have until the Monday after the election to show ID to your county clerk.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir de Arkansas, un pasaporte de EE. UU. o una licencia para portar armas ocultas (concealed carry license). Si no tienes una, votas con una boleta provisional y tienes hasta el lunes después de la elección para mostrar tu identificación al secretario de tu condado (county clerk)."),
        'reg': ("No, not for the November 3 election. The deadline was October 5, 2026, and Arkansas has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 5 de octubre de 2026, y Arkansas no tiene registro el Día de las Elecciones."),
    },
    'CA': {
        'mail': ("Every voter gets a ballot by mail. Mail it postmarked by November 3 so it arrives within seven days, or drop it off by 8 p.m. that day. Polling places and vote centers are open 7 a.m. to 8 p.m. on November 3.",
                 "Todos los votantes reciben una boleta por correo. Envíala con matasellos del 3 de noviembre o antes para que llegue en un plazo de siete días, o entrégala a más tardar a las 8 p. m. de ese día. Los lugares y centros de votación abren de 7 a. m. a 8 p. m. el 3 de noviembre."),
        'id': ("Usually not. Most California voters don't show ID. Some first-time voters who registered without an ID number may be asked for one, such as a driver's license, passport, or utility bill.",
               "Por lo general, no. La mayoría de los votantes de California no muestran identificación. A algunos votantes nuevos que se registraron sin un número de identificación se les puede pedir una, como una licencia de conducir, un pasaporte o un recibo de servicios."),
        'reg': ("Yes. The regular deadline is October 19, 2026. After that, you can register and vote the same day at your county elections office, a vote center, or a polling place, through Election Day.",
                "Sí. La fecha límite regular es el 19 de octubre de 2026. Después, puedes registrarte y votar el mismo día en la oficina electoral de tu condado, en un centro de votación o en un lugar de votación, hasta el Día de las Elecciones."),
        'selfie': ("Yes. Since 2017, California law lets you photograph your own ballot and share it. You can't photograph other voters going in or out.",
                   "Sí. Desde 2017, la ley de California te permite tomarle foto a tu propia boleta y compartirla. No puedes tomar fotos de otros votantes al entrar o salir."),
    },
    'CO': {
        'mail': ("Colorado votes by mail. Your ballot must reach your county clerk by 7 p.m. on November 3, and postmarks don't count. Voter service and polling centers are open for in-person voting until 7 p.m. that day.",
                 "Colorado vota por correo. Tu boleta debe llegar a la oficina electoral de tu condado (county clerk) a más tardar a las 7 p. m. del 3 de noviembre, y el matasellos no cuenta. Los centros de votación y servicio al votante están abiertos para votar en persona hasta las 7 p. m. de ese día."),
        'id': ("If you vote in person, yes. Most people bring a Colorado driver's license or ID card, and many other IDs are accepted.",
               "Si votas en persona, sí. La mayoría lleva una licencia de conducir o una identificación de Colorado, y se aceptan muchas otras identificaciones."),
        'reg': ("Yes. Register online or by mail by October 26, 2026 to get a ballot by mail. After that, you can register and vote in person at a voter service and polling center through Election Day.",
                "Sí. Regístrate en línea o por correo a más tardar el 26 de octubre de 2026 para recibir una boleta por correo. Después, puedes registrarte y votar en persona en un centro de votación y servicio al votante hasta el Día de las Elecciones."),
        'selfie': ("Yes. A 2017 law lets you show your voted ballot to others, including in a photo. County clerks can set reasonable limits on photos at voting centers.",
                   "Sí. Una ley de 2017 te permite mostrar tu boleta votada a otras personas, también en una foto. Las oficinas electorales de los condados pueden poner límites razonables a las fotos en los centros de votación."),
    },
    'CT': {
        'polls': ("Polls are open 6 a.m. to 8 p.m. If you're in line by 8 p.m., you can vote.",
                  "Los lugares de votación abren de 6 a. m. a 8 p. m. Si estás en la fila a las 8 p. m., puedes votar."),
        'id': ("You'll be asked for ID, such as a Social Security card or another preprinted ID with your name and your address, signature, or photo. If you don't have one, you can sign a statement instead.",
               "Te pedirán una identificación, como tu tarjeta del Seguro Social u otra identificación impresa con tu nombre y tu dirección, firma o foto. Si no tienes una, puedes firmar una declaración en su lugar."),
        'reg': ("Yes. The regular deadline was October 16, 2026, but you can register and vote the same day during early voting, October 19 to November 1, or on Election Day at your town's Same Day Registration location.",
                "Sí. La fecha límite regular fue el 16 de octubre de 2026, pero puedes registrarte y votar el mismo día durante la votación anticipada, del 19 de octubre al 1 de noviembre, o el Día de las Elecciones en el lugar de registro el mismo día (Same Day Registration) de tu municipio."),
    },
    'DE': {
        'polls': ("Polls are open 7 a.m. to 8 p.m., and you vote at your assigned polling place.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m., y votas en el lugar de votación que te corresponde."),
        'id': ("Yes. Show proof of identity, such as a photo ID, a utility bill, or a bank statement. If you don't have it with you, you can sign an affidavit and vote.",
               "Sí. Muestra un comprobante de identidad, como una identificación con foto, un recibo de servicios o un estado de cuenta bancario. Si no lo traes contigo, puedes firmar una declaración jurada y votar."),
        'reg': ("No, not for the November 3 election. The deadline was October 10, 2026, and Delaware has no same-day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 10 de octubre de 2026, y Delaware no tiene registro el mismo día."),
    },
    'DC': {
        'polls': ("Vote centers are open 7 a.m. to 8 p.m. on November 3, and you can vote at any of them. Every registered voter also gets a ballot by mail. Mail it postmarked by November 3, or use a drop box by 8 p.m.",
                  "Los centros de votación abren de 7 a. m. a 8 p. m. el 3 de noviembre, y puedes votar en cualquiera. Todos los votantes registrados también reciben una boleta por correo. Envíala con matasellos del 3 de noviembre o antes, o déjala en un buzón de entrega de boletas a más tardar a las 8 p. m."),
        'id': ("Usually not. If you registered by mail or online for the first time, you may need to show ID with your name and address, such as a photo ID or a utility bill.",
               "Por lo general, no. Si te registraste por primera vez por correo o en línea, es posible que tengas que mostrar una identificación con tu nombre y dirección, como una identificación con foto o un recibo de servicios."),
        'reg': ("Yes. Online and mail registrations had to arrive by October 13, 2026, but you can register and vote in person during early voting or on Election Day. Bring proof of where you live.",
                "Sí. Los registros en línea y por correo tenían que llegar a más tardar el 13 de octubre de 2026, pero puedes registrarte y votar en persona durante la votación anticipada o el Día de las Elecciones. Lleva un comprobante de domicilio."),
    },
    'FL': {
        'polls': ("Polls are open 7 a.m. to 7 p.m. local time. Florida has two time zones.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m., hora local. Florida tiene dos zonas horarias."),
        'id': ("Yes. Show a current photo ID with your signature, such as a Florida driver license, a Florida ID card, or a U.S. passport. Without one, you vote a provisional ballot.",
               "Sí. Muestra una identificación vigente con foto y tu firma, como una licencia de conducir de Florida, una tarjeta de identificación de Florida o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional."),
        'reg': ("No, not for the November 3 election. The deadline was October 5, 2026, and Florida has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 5 de octubre de 2026, y Florida no tiene registro el Día de las Elecciones."),
        'selfie': ("Yes, you can photograph your own ballot. No other photos are allowed in the polling room or early voting area.",
                   "Sí, puedes tomarle foto a tu propia boleta. No se permiten otras fotos en la sala de votación ni en el área de votación anticipada."),
    },
    'MT': {
        'polls': ("Most polling places are open 7 a.m. to 8 p.m. A polling place with fewer than 400 voters may open as late as noon, and the county mails a notice if it does.",
                  "La mayoría de los lugares de votación abren de 7 a. m. a 8 p. m. Un lugar de votación con menos de 400 votantes puede abrir hasta el mediodía, y el condado envía un aviso por correo si lo hace."),
        'id': ("Yes. Show a photo ID such as a Montana driver's license, a state ID card, a U.S. passport, or a tribal photo ID. Another photo ID works if you also bring a utility bill or bank statement with your name and address.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir de Montana, una tarjeta de identificación estatal, un pasaporte de EE. UU. o una identificación tribal con foto. Otra identificación con foto sirve si también llevas un recibo de servicios o un estado de cuenta bancario con tu nombre y dirección."),
        'reg': ("Yes. Regular registration closed October 5, 2026, but you can register late at your county election office and vote there until noon on Election Day.",
                "Sí. El registro regular cerró el 5 de octubre de 2026, pero puedes registrarte tarde en la oficina electoral de tu condado y votar ahí hasta el mediodía del Día de las Elecciones."),
        'selfie': ("Yes. Montana's Commissioner of Political Practices says a selfie with your own marked ballot, shared with friends or on social media, doesn't break state law.",
                   "Sí. El Comisionado de Prácticas Políticas de Montana (Commissioner of Political Practices) dice que una selfie con tu propia boleta marcada, compartida con amigos o en redes sociales, no viola la ley estatal."),
    },
    'NE': {
        'polls': ("Polls are open 8 a.m. to 8 p.m. in the Central Time Zone and 7 a.m. to 7 p.m. in the Mountain Time Zone.",
                  "Los lugares de votación abren de 8 a. m. a 8 p. m. en la zona horaria del Centro y de 7 a. m. a 7 p. m. en la zona horaria de la Montaña."),
        'id': ("Yes. Show a photo ID with your name and photo, such as a Nebraska driver's license, a state ID, or a U.S. passport. Without one, you vote a provisional ballot.",
               "Sí. Muestra una identificación con tu nombre y foto, como una licencia de conducir de Nebraska, una identificación estatal o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional."),
        'reg': ("Only in person at your county election office, until 6 p.m. on October 23, 2026. Online and mail registration closed October 16, and Nebraska has no Election Day registration.",
                "Solo en persona en la oficina electoral de tu condado, hasta las 6 p. m. del 23 de octubre de 2026. El registro en línea y por correo cerró el 16 de octubre, y Nebraska no tiene registro el Día de las Elecciones."),
        'selfie': ("Yes. Nebraska law lets you photograph your own marked ballot and share the photo. No one may pressure you to show your ballot.",
                   "Sí. La ley de Nebraska te permite tomarle foto a tu propia boleta marcada y compartir la foto. Nadie puede presionarte para que muestres tu boleta."),
    },
    'NV': {
        'polls': ("Polling places are open 7 a.m. to 7 p.m. Nevada also mails a ballot to every active voter. Mail it postmarked by Election Day so it arrives by 5 p.m. on November 7, or drop it off before polls close.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. Nevada también envía una boleta por correo a cada votante activo. Envíala con matasellos del Día de las Elecciones o antes para que llegue a más tardar a las 5 p. m. del 7 de noviembre, o entrégala antes de que cierren los lugares de votación."),
        'id': ("Usually not; most voters sign to vote. Some first-time voters who registered by mail or online may need a photo ID, a utility bill, or a bank statement.",
               "Por lo general, no; la mayoría de los votantes firman para votar. Algunos votantes nuevos que se registraron por correo o en línea podrían necesitar una identificación con foto, un recibo de servicios o un estado de cuenta bancario."),
        'reg': ("Yes. You can register online through Election Day, or register and vote in person during early voting or on Election Day. Mail registration closed October 6, 2026.",
                "Sí. Puedes registrarte en línea hasta el Día de las Elecciones, o registrarte y votar en persona durante la votación anticipada o el Día de las Elecciones. El registro por correo cerró el 6 de octubre de 2026."),
        'selfie': ("Not at the polls. Nevada law bars the public from photographing voting at a polling place.",
                   "No en el lugar de votación. La ley de Nevada prohíbe que el público tome fotos de la votación en un lugar de votación."),
    },
    'NH': {
        'polls': ("Hours vary by town and city. Polls open by 11 a.m. and close no earlier than 7 p.m., and many open earlier. Your town or city clerk has the exact hours.",
                  "El horario cambia según el municipio o la ciudad. Los lugares de votación abren a más tardar a las 11 a. m. y cierran a las 7 p. m. o después, y muchos abren antes. El secretario de tu municipio o ciudad (town or city clerk) tiene el horario exacto."),
        'id': ("Yes. Show a valid photo ID, such as a driver's license from any state, a state ID card, a U.S. military ID, or a U.S. passport. Without one, an election official must confirm who you are before you can vote.",
               "Sí. Muestra una identificación válida con foto, como una licencia de conducir de cualquier estado, una tarjeta de identificación estatal, una identificación militar de EE. UU. o un pasaporte de EE. UU. Si no tienes una, un funcionario electoral debe confirmar quién eres antes de que puedas votar."),
        'reg': ("Yes. You can register with your town or city clerk before the last checklist session in late October, or at your polling place on Election Day. You'll need proof of U.S. citizenship.",
                "Sí. Puedes registrarte con el secretario de tu municipio o ciudad antes de la última sesión de revisión del padrón (checklist) a fines de octubre, o en tu lugar de votación el Día de las Elecciones. Necesitarás un comprobante de ciudadanía estadounidense."),
    },
    'NJ': {
        'polls': ("Polls are open 6 a.m. to 8 p.m. In-person early voting runs October 24 to November 1.",
                  "Los lugares de votación abren de 6 a. m. a 8 p. m. La votación anticipada en persona es del 24 de octubre al 1 de noviembre."),
        'id': ("Usually not. Some first-time voters who registered by mail without a verified ID number must show ID, such as a driver's license or a utility bill, or vote a provisional ballot.",
               "Por lo general, no. Algunos votantes nuevos que se registraron por correo sin un número de identificación verificado deben mostrar una identificación, como una licencia de conducir o un recibo de servicios, o votar con una boleta provisional."),
        'reg': ("No, not for the November 3 election. The deadline was October 13, 2026, and New Jersey has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 13 de octubre de 2026, y New Jersey no tiene registro el Día de las Elecciones."),
        'selfie': ("Not at the polls. New Jersey law bars showing your ballot inside the polling room.",
                   "No en el lugar de votación. La ley de New Jersey prohíbe mostrar tu boleta dentro de la sala de votación."),
    },
    'NM': {
        'polls': ("Polling places are open 7 a.m. to 7 p.m. Mailed ballots must arrive by 7 p.m. on Election Day.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. Las boletas enviadas por correo deben llegar a más tardar a las 7 p. m. del Día de las Elecciones."),
        'id': ("Usually not. You state your name, year of birth, and registered address. First-time voters who registered by mail without ID must show one, such as a photo ID or a utility bill.",
               "Por lo general, no. Dices tu nombre, tu año de nacimiento y la dirección con la que te registraste. Los votantes nuevos que se registraron por correo sin identificación deben mostrar una, como una identificación con foto o un recibo de servicios."),
        'reg': ("Yes. Regular registration closed October 6, 2026, but you can register and vote the same day at your County Clerk's office, some early voting sites, or any polling place in your county on Election Day.",
                "Sí. El registro regular cerró el 6 de octubre de 2026, pero puedes registrarte y votar el mismo día en la oficina del secretario de tu condado (County Clerk), en algunos centros de votación anticipada o en cualquier lugar de votación de tu condado el Día de las Elecciones."),
    },
    'NY': {
        'polls': ("Polls are open 6 a.m. to 9 p.m. Early voting runs October 24 to November 1, with hours set by each county.",
                  "Los lugares de votación abren de 6 a. m. a 9 p. m. La votación anticipada es del 24 de octubre al 1 de noviembre, con horarios que fija cada condado."),
        'id': ("Usually not; you sign the poll book. Some first-time voters who registered by mail may need a photo ID, a utility bill, a bank statement, or a paycheck.",
               "Por lo general, no; firmas el registro de votantes. Algunos votantes nuevos que se registraron por correo podrían necesitar una identificación con foto, un recibo de servicios, un estado de cuenta bancario o un talón de pago."),
        'reg': ("Until October 24, 2026. Your application must reach your county board of elections by then, and New York has no Election Day registration.",
                "Hasta el 24 de octubre de 2026. Tu solicitud debe llegar a la junta electoral de tu condado (county board of elections) a más tardar ese día, y New York no tiene registro el Día de las Elecciones."),
        'selfie': ("No. New York law makes it a misdemeanor to show your marked ballot to anyone in a way that reveals your votes, and that includes a ballot selfie.",
                   "No. En New York es un delito menor mostrar tu boleta marcada a cualquier persona de una forma que revele tus votos, y eso incluye una selfie con tu boleta."),
    },
    'NC': {
        'polls': ("Polling places are open 6:30 a.m. to 7:30 p.m. If you're in line at 7:30 p.m., you can still vote.",
                  "Los lugares de votación abren de 6:30 a. m. a 7:30 p. m. Si estás en la fila a las 7:30 p. m., todavía puedes votar."),
        'id': ("You'll be asked for a photo ID, such as a North Carolina driver's license, a U.S. passport, or a free voter photo ID card. If you can't show one, you can still vote by filling out an ID Exception Form.",
               "Te pedirán una identificación con foto, como una licencia de conducir de North Carolina, un pasaporte de EE. UU. o una credencial gratuita de votante con foto. Si no puedes mostrar una, todavía puedes votar si llenas un formulario de excepción de identificación (ID Exception Form)."),
        'reg': ("Yes, during early voting, through October 31. You can register and vote at the same time at an early voting site, but not on Election Day.",
                "Sí, durante la votación anticipada, hasta el 31 de octubre. Puedes registrarte y votar al mismo tiempo en un centro de votación anticipada, pero no el Día de las Elecciones."),
        'selfie': ("No. North Carolina law bars photographing a voted ballot. It also bars photographing voters in the voting area without their permission and the chief judge's.",
                   "No. La ley de North Carolina prohíbe tomarle foto a una boleta votada. También prohíbe tomar fotos de los votantes en el área de votación sin su permiso y el del juez principal (chief judge)."),
    },
    'ND': {
        'polls': ("Hours vary by county or city. Polls open between 7 a.m. and 9 a.m. and close between 7 p.m. and 9 p.m. local time. North Dakota has two time zones.",
                  "El horario cambia según el condado o la ciudad. Los lugares de votación abren entre las 7 a. m. y las 9 a. m. y cierran entre las 7 p. m. y las 9 p. m., hora local. North Dakota tiene dos zonas horarias."),
        'id': ("Yes. Show a North Dakota driver's license, nondriver ID card, or tribal ID with your name, current North Dakota address, and birth date. A utility bill or bank statement can fill in missing details.",
               "Sí. Muestra una licencia de conducir de North Dakota, una tarjeta de identificación para no conductores o una identificación tribal con tu nombre, tu dirección actual en North Dakota y tu fecha de nacimiento. Un recibo de servicios o un estado de cuenta bancario puede completar los datos que falten."),
        'reg': ("You don't need to. North Dakota has no voter registration. If you're eligible, bring a valid ID with your current address and vote.",
                "No hace falta. North Dakota no tiene registro de votantes. Si cumples los requisitos, lleva una identificación válida con tu dirección actual y vota."),
    },
    'OH': {
        'polls': ("Polls are open 6:30 a.m. to 7:30 p.m., and you vote at your assigned polling place.",
                  "Los lugares de votación abren de 6:30 a. m. a 7:30 p. m., y votas en el lugar de votación que te corresponde."),
        'id': ("Yes. Show an unexpired photo ID, such as an Ohio driver's license, a state ID card, a U.S. passport, or a military ID. Without one, you vote a provisional ballot.",
               "Sí. Muestra una identificación vigente con foto, como una licencia de conducir de Ohio, una tarjeta de identificación estatal, un pasaporte de EE. UU. o una identificación militar. Si no tienes una, votas con una boleta provisional."),
        'reg': ("No, not for the November 3 election. The deadline was October 5, 2026, and Ohio has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 5 de octubre de 2026, y Ohio no tiene registro el Día de las Elecciones."),
    },
    'OK': {
        'polls': ("Polls are open 7 a.m. to 7 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m."),
        'id': ("Yes. Show a current photo ID, such as a driver's license, or the free voter ID card your county election board mailed you. Without either, you can sign an affidavit and vote a provisional ballot.",
               "Sí. Muestra una identificación vigente con foto, como una licencia de conducir, o la tarjeta gratuita de votante que te envió por correo la junta electoral de tu condado. Si no tienes ninguna, puedes firmar una declaración jurada y votar con una boleta provisional."),
        'reg': ("No, not for the November 3 election. The deadline was October 9, 2026, and Oklahoma has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 9 de octubre de 2026, y Oklahoma no tiene registro el Día de las Elecciones."),
        'selfie': ("Yes. You can take a picture of your marked ballot, but don't post or share it until you're outside the area where you check in and vote.",
                   "Sí. Puedes tomarle foto a tu boleta marcada, pero no la publiques ni la compartas hasta que salgas del área donde te registras y votas."),
    },
}

RESEARCH = ['states-a.json', 'states-b.json']
KEYMAP = {'polls': 'polls', 'mail': 'polls', 'id': 'id', 'reg': 'registration', 'selfie': 'selfie'}


def php(s):
    return "'" + s.replace('\\', '\\\\').replace("'", "\\'") + "'"


def main(research_dir):
    research = {}
    for f in RESEARCH:
        p = Path(research_dir) / f
        if p.exists():
            for r in json.loads(p.read_text()):
                research[r['abbr']] = r
    lines = ['<?php', '// Generated by build/faq_text.py from verified research (docs/faq-sources.json). Edit faq_text.py, not this file.', 'return array(']
    sources = {}
    for abbr, qs in A.items():
        r = research[abbr]
        lines.append(f"\t'{abbr}' => array(")
        for key, (en, es) in qs.items():
            src = r[KEYMAP[key]]['source']
            assert '—' not in en + es and '–' not in en + es, (abbr, key)
            lines.append(f"\t\t'{key}' => array( 'en' => {php(en)}, 'es' => {php(es)}, 'src' => {php(src)} ),")
        lines.append('\t),')
        sources[abbr] = {k: r.get(k) for k in ('polls', 'id', 'registration', 'selfie', 'notes')}
    lines.append(');')
    (ROOT / 'site' / 'faq-states.php').write_text('\n'.join(lines) + '\n')
    (ROOT / 'docs' / 'faq-sources.json').write_text(json.dumps(sources, indent=1, ensure_ascii=False) + '\n')
    print(f'{len(A)} states, {sum(len(q) for q in A.values())} answers')


if __name__ == '__main__':
    import sys
    main(sys.argv[1] if len(sys.argv) > 1 else str(ROOT / 'build' / 'faq-research'))
