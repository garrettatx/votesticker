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
        'id': ("Yes. Show a photo ID, such as an Arkansas driver's license, a U.S. passport, or a concealed carry license. Without one, you vote a provisional ballot and have until noon on the Monday after the election to show ID to your county clerk.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir de Arkansas, un pasaporte de EE. UU. o una licencia para portar armas ocultas (concealed carry license). Si no tienes una, votas con una boleta provisional y tienes hasta el mediodía del lunes después de la elección para mostrar tu identificación al secretario de tu condado (county clerk)."),
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
        'reg': ("Yes, in person. Regular registration closed October 5, 2026. You can still register late at your county election office and vote there; ask your county for the cutoff, because a court has blocked the 2025 law that set one.",
                "Sí, en persona. El registro regular cerró el 5 de octubre de 2026. Todavía puedes registrarte tarde en la oficina electoral de tu condado y votar ahí; pregunta a tu condado la hora límite, porque un tribunal bloqueó la ley de 2025 que la fijaba."),
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

NO_REG = ("No, not for the November 3 election. The deadline was {d}, and {s} has no Election Day registration.",
          "No para la elección del 3 de noviembre. La fecha límite fue el {de}, y {s} no tiene registro el Día de las Elecciones.")


def no_reg(state, d, de):
    return (NO_REG[0].format(d=d, s=state), NO_REG[1].format(de=de, s=state))


A.update({
    'GA': {
        'polls': ("Polls are open 7 a.m. to 7 p.m. at your assigned polling place. If you're in line by 7 p.m., you can still vote.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. en el lugar que te corresponde. Si estás en la fila a las 7 p. m., todavía puedes votar."),
        'id': ("Yes. Show a photo ID, such as a Georgia driver's license, even if it's expired, a U.S. passport, or a free Georgia voter ID card from your county registrar.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir de Georgia, aunque esté vencida, un pasaporte de EE. UU. o una credencial gratuita de votante de Georgia de la oficina de registro de tu condado (county registrar)."),
        'reg': no_reg('Georgia', 'October 5, 2026', '5 de octubre de 2026'),
        'selfie': ("Not at the polls. Georgia law bars using cameras or cell phones in a polling place while voting is going on, unless a poll manager allows it.",
                   "No en el lugar de votación. La ley de Georgia prohíbe usar cámaras o celulares en un lugar de votación mientras se está votando, salvo que el encargado de la casilla (poll manager) lo permita."),
    },
    'HI': {
        'mail': ("Hawaii votes by mail. Your ballot must be received by 7 p.m. on November 3, and a postmark isn't enough. Voter service centers are open 7 a.m. to 7 p.m. that day for in-person voting.",
                 "Hawaii vota por correo. Tu boleta debe recibirse a más tardar a las 7 p. m. del 3 de noviembre, y el matasellos no basta. Los centros de servicio al votante abren de 7 a. m. a 7 p. m. ese día para votar en persona."),
        'id': ("Usually not. At a voter service center, a clerk may ask you to confirm who you are, for example with a photo ID.",
               "Por lo general, no. En un centro de servicio al votante, un empleado puede pedirte que confirmes quién eres, por ejemplo con una identificación con foto."),
        'reg': ("Yes. You can register and vote the same day at a voter service center, open October 20 through November 3.",
                "Sí. Puedes registrarte y votar el mismo día en un centro de servicio al votante, abierto del 20 de octubre al 3 de noviembre."),
    },
    'ID': {
        'polls': ("Polls are open 8 a.m. to 8 p.m. local time, and some counties open at 7 a.m. Idaho has two time zones.",
                  "Los lugares de votación abren de 8 a. m. a 8 p. m., hora local, y algunos condados abren a las 7 a. m. Idaho tiene dos zonas horarias."),
        'id': ("You'll be asked for a photo ID, such as an Idaho driver's license, a U.S. passport, or a tribal ID. If you don't have one, you can sign an identity affidavit instead.",
               "Te pedirán una identificación con foto, como una licencia de conducir de Idaho, un pasaporte de EE. UU. o una identificación tribal. Si no tienes una, puedes firmar una declaración jurada de identidad en su lugar."),
        'reg': ("Yes. Preregistration is open until October 23, 2026. After that, you can register and vote in person during early voting or on Election Day with a photo ID and proof of where you live.",
                "Sí. El registro previo está abierto hasta el 23 de octubre de 2026. Después, puedes registrarte y votar en persona durante la votación anticipada o el Día de las Elecciones con una identificación con foto y un comprobante de domicilio."),
    },
    'IL': {
        'polls': ("Polls are open 6 a.m. to 7 p.m.",
                  "Los lugares de votación abren de 6 a. m. a 7 p. m."),
        'id': ("Usually not; most voters give their name and sign in. If you first registered by mail and haven't proven your identity yet, you may need to show ID.",
               "Por lo general, no; la mayoría de los votantes dan su nombre y firman. Si te registraste por primera vez por correo y todavía no has comprobado tu identidad, es posible que tengas que mostrar una identificación."),
        'reg': ("Yes. Online registration is open through October 18, 2026. After that, you can register and vote in person at grace period sites through Election Day.",
                "Sí. El registro en línea está abierto hasta el 18 de octubre de 2026. Después, puedes registrarte y votar en persona en los lugares del periodo de gracia (grace period) hasta el Día de las Elecciones."),
    },
    'IN': {
        'polls': ("Polls are open 6 a.m. to 6 p.m. local time. Indiana has two time zones.",
                  "Los lugares de votación abren de 6 a. m. a 6 p. m., hora local. Indiana tiene dos zonas horarias."),
        'id': ("Yes. Show a current photo ID issued by Indiana or the U.S. government, such as a driver's license or a U.S. passport. Without one, you vote a provisional ballot and have until noon 10 days after the election to follow up with your county election board.",
               "Sí. Muestra una identificación vigente con foto emitida por Indiana o por el gobierno de EE. UU., como una licencia de conducir o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional y tienes hasta el mediodía del décimo día después de la elección para completar el trámite con la junta electoral de tu condado."),
        'reg': no_reg('Indiana', 'October 5, 2026', '5 de octubre de 2026'),
    },
    'IA': {
        'polls': ("Polls are open 7 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m."),
        'id': ("Yes. Show an ID such as an Iowa driver's license, a U.S. passport, or the free Iowa Voter ID Card. Without one, another registered voter can vouch for you.",
               "Sí. Muestra una identificación, como una licencia de conducir de Iowa, un pasaporte de EE. UU. o la credencial gratuita Iowa Voter ID Card. Si no tienes una, otro votante registrado puede dar fe de quién eres."),
        'reg': ("Yes. Preregistration closes October 19, 2026. After that, you can register and vote at your polling place on Election Day with ID and proof of where you live.",
                "Sí. El registro previo cierra el 19 de octubre de 2026. Después, puedes registrarte y votar en tu lugar de votación el Día de las Elecciones con una identificación y un comprobante de domicilio."),
        'selfie': ("Yes, with limits. Iowa bans photos and showing your voted ballot only when it's for an illegal purpose or it interferes with other voters or the polling place.",
                   "Sí, con límites. Iowa prohíbe las fotos y mostrar tu boleta votada solo cuando es con un fin ilegal o cuando interfiere con otros votantes o con el lugar de votación."),
    },
    'KS': {
        'polls': ("Polls are open at least 7 a.m. to 7 p.m. Counties can open as early as 6 a.m. and close as late as 8 p.m., so check your county's hours.",
                  "Los lugares de votación abren por lo menos de 7 a. m. a 7 p. m. Los condados pueden abrir desde las 6 a. m. y cerrar hasta las 8 p. m., así que revisa el horario de tu condado."),
        'id': ("Yes. Show a photo ID, such as a driver's license or a U.S. passport. Without one, you vote a provisional ballot.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional."),
        'reg': no_reg('Kansas', 'October 13, 2026', '13 de octubre de 2026'),
    },
    'KY': {
        'polls': ("Polls are open 6 a.m. to 6 p.m. local time. Kentucky has two time zones.",
                  "Los lugares de votación abren de 6 a. m. a 6 p. m., hora local. Kentucky tiene dos zonas horarias."),
        'id': ("You'll be asked for a photo ID, such as a Kentucky driver's license, a U.S. passport, or a military ID. If you can't get one for a reason the law lists, such as your work schedule, a lost ID or no transportation, you can sign an affirmation and vote, but you still have to show an ID card with your name and photo, or a county ID card approved by the State Board of Elections.",
               "Te pedirán una identificación con foto, como una licencia de conducir de Kentucky, un pasaporte de EE. UU. o una identificación militar. Si no puedes conseguir una por una razón que marca la ley, como tu horario de trabajo, haber perdido tu identificación o no tener transporte, puedes firmar una declaración y votar, pero igual tienes que mostrar una tarjeta de identificación con tu nombre y tu foto, o una identificación de un condado aprobada por la Junta Estatal de Elecciones (State Board of Elections)."),
        'reg': no_reg('Kentucky', 'October 5, 2026', '5 de octubre de 2026'),
    },
    'LA': {
        'polls': ("Polls are open 6 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 6 a. m. a 8 p. m."),
        'id': ("You'll be asked for a photo ID with your signature, such as a Louisiana driver's license or special ID card. Without one, you can sign a voter affidavit and vote.",
               "Te pedirán una identificación con foto y tu firma, como una licencia de conducir de Louisiana o una tarjeta de identificación especial. Si no tienes una, puedes firmar una declaración jurada de votante y votar."),
        'reg': ("No, not for the November 3 election. The deadlines were October 5 in person or by mail and October 13, 2026 online, and Louisiana has no Election Day registration.",
                "No para la elección del 3 de noviembre. Las fechas límite fueron el 5 de octubre en persona o por correo y el 13 de octubre de 2026 en línea, y Louisiana no tiene registro el Día de las Elecciones."),
    },
    'ME': {
        'polls': ("Polls close at 8 p.m. Each town sets its opening time, between 6 a.m. and 8 a.m., or as late as 10 a.m. in towns with fewer than 500 people.",
                  "Los lugares de votación cierran a las 8 p. m. Cada municipio fija su hora de apertura, entre las 6 a. m. y las 8 a. m., o hasta las 10 a. m. en municipios de menos de 500 habitantes."),
        'id': ("No, if you're already registered. You state your name and address to the election clerk. If you register in person, you must show proof of identity and residence.",
               "No, si ya estás registrado. Le dices tu nombre y tu dirección al empleado electoral. Si te registras en persona, debes mostrar un comprobante de identidad y de domicilio."),
        'reg': ("Yes. Mail and online registration closed October 13, 2026, but you can register in person with your town's registrar through Election Day, including at the polls.",
                "Sí. El registro por correo y en línea cerró el 13 de octubre de 2026, pero puedes registrarte en persona con el registrador de tu municipio hasta el Día de las Elecciones, incluso en el lugar de votación."),
    },
    'MD': {
        'polls': ("Polls are open 7 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m."),
        'id': ("Usually not. Some first-time voters are asked for ID, such as a Maryland driver's license, a student ID, or a utility bill.",
               "Por lo general, no. A algunos votantes nuevos se les pide una identificación, como una licencia de conducir de Maryland, una identificación de estudiante o un recibo de servicios."),
        'reg': ("Yes. The advance deadline was October 13, 2026, but you can register and vote during early voting or at your polling place on Election Day. Bring a document that shows where you live.",
                "Sí. La fecha límite anticipada fue el 13 de octubre de 2026, pero puedes registrarte y votar durante la votación anticipada o en tu lugar de votación el Día de las Elecciones. Lleva un documento que muestre tu domicilio."),
        'selfie': ("Yes, for personal use. You can photograph your own marked ballot before you cast it. Otherwise, phones and cameras can't be used in polling places.",
                   "Sí, para uso personal. Puedes tomarle foto a tu propia boleta marcada antes de depositarla. Fuera de eso, no se pueden usar celulares ni cámaras en los lugares de votación."),
    },
    'MA': {
        'polls': ("Polls close at 8 p.m. They open by 7 a.m., and some cities and towns open as early as 5:45 a.m.",
                  "Los lugares de votación cierran a las 8 p. m. Abren a más tardar a las 7 a. m., y algunas ciudades y municipios abren desde las 5:45 a. m."),
        'id': ("Usually not. Some first-time voters who registered by mail must show a photo ID or a document like a utility bill, and poll workers may ask anyone for ID.",
               "Por lo general, no. Algunos votantes nuevos que se registraron por correo deben mostrar una identificación con foto o un documento como un recibo de servicios, y los trabajadores electorales pueden pedirle una identificación a cualquier votante."),
        'reg': ("Until October 24, 2026, at 5 p.m., or by mail postmarked that day. Massachusetts has no Election Day registration.",
                "Hasta el 24 de octubre de 2026 a las 5 p. m., o por correo con matasellos de ese día. Massachusetts no tiene registro el Día de las Elecciones."),
        'selfie': ("No. Massachusetts law makes it a crime to let anyone see how you marked your ballot for a purpose the law doesn't allow, and a photo you share would show it.",
                   "No. En Massachusetts es un delito dejar que alguien vea cómo marcaste tu boleta con un fin que la ley no permite, y una foto que compartas lo mostraría."),
    },
    'MI': {
        'polls': ("Polls are open 7 a.m. to 8 p.m. local time.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m., hora local."),
        'id': ("If you have a photo ID, such as a driver's license, a U.S. passport, or a student ID, you'll be asked to show it. If you don't have one, you can sign an affidavit and vote.",
               "Si tienes una identificación con foto, como una licencia de conducir, un pasaporte de EE. UU. o una identificación de estudiante, te pedirán que la muestres. Si no tienes una, puedes firmar una declaración jurada y votar."),
        'reg': ("Yes. Online and mail registration closes October 19, 2026. After that, you can register in person at your city or township clerk's office through 8 p.m. on Election Day, with proof of residency.",
                "Sí. El registro en línea y por correo cierra el 19 de octubre de 2026. Después, puedes registrarte en persona en la oficina del secretario de tu ciudad o municipio (clerk) hasta las 8 p. m. del Día de las Elecciones, con un comprobante de domicilio."),
    },
    'MN': {
        'polls': ("Polls are open 7 a.m. to 8 p.m. Some small towns open later, but no later than 10 a.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m. Algunos pueblos pequeños abren más tarde, pero no después de las 10 a. m."),
        'id': ("No, if you're already registered; you sign the polling place roster. If you register at the polls, you'll need proof of where you live.",
               "No, si ya estás registrado; firmas la lista de votantes del lugar de votación. Si te registras ahí, necesitarás un comprobante de domicilio."),
        'reg': ("Yes. Preregistration closed October 13, 2026, but you can register at your polling place on Election Day. Bring proof of where you live.",
                "Sí. El registro previo cerró el 13 de octubre de 2026, pero puedes registrarte en tu lugar de votación el Día de las Elecciones. Lleva un comprobante de domicilio."),
        'selfie': ("Not at the polls. If you show your marked ballot to anyone at the polling place, election judges set it aside as spoiled.",
                   "No en el lugar de votación. Si le muestras tu boleta marcada a alguien en el lugar de votación, los jueces electorales la anulan."),
    },
    'MS': {
        'polls': ("Polls are open 7 a.m. to 7 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m."),
        'id': ("Yes. Show a photo ID, such as a Mississippi driver's license or a U.S. passport. Without one, you vote an affidavit ballot and have five business days to show ID.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir de Mississippi o un pasaporte de EE. UU. Si no tienes una, votas con una boleta de declaración jurada (affidavit ballot) y tienes cinco días hábiles para mostrar tu identificación."),
        'reg': no_reg('Mississippi', 'October 5, 2026', '5 de octubre de 2026'),
    },
    'MO': {
        'polls': ("Polls are open 6 a.m. to 7 p.m. If you're in line at 7 p.m., you can still vote.",
                  "Los lugares de votación abren de 6 a. m. a 7 p. m. Si estás en la fila a las 7 p. m., todavía puedes votar."),
        'id': ("Yes. Show a photo ID, such as a Missouri driver's license or a U.S. passport. Without one, you vote a provisional ballot, which counts if you come back with ID that day or your signature matches.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir de Missouri o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional, que cuenta si regresas con tu identificación ese mismo día o si tu firma coincide."),
        'reg': no_reg('Missouri', 'October 7, 2026', '7 de octubre de 2026'),
        'selfie': ("No. Missouri law makes it an election offense to let anyone see your ballot so they know how you voted, and a photo you share would do that.",
                   "No. En Missouri es un delito electoral dejar que alguien vea tu boleta para que sepa cómo votaste, y una foto que compartas lo haría."),
    },
    'OR': {
        'mail': ("Oregon votes by mail. Drop your ballot at a drop box or county elections office by 8 p.m. on Election Day, or mail it postmarked by Election Day so it arrives within seven days.",
                 "Oregon vota por correo. Deja tu boleta en un buzón de entrega o en la oficina electoral de tu condado a más tardar a las 8 p. m. del Día de las Elecciones, o envíala con matasellos del Día de las Elecciones o antes para que llegue en un plazo de siete días."),
        'id': ("No. Oregon votes by mail, and your county checks the signature on your ballot envelope against your registration.",
               "No. Oregon vota por correo, y tu condado compara la firma del sobre de tu boleta con la de tu registro."),
        'reg': no_reg('Oregon', 'October 13, 2026', '13 de octubre de 2026'),
    },
    'PA': {
        'polls': ("Polls are open 7 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m."),
        'id': ("Usually not. If it's your first time voting at your polling place, bring an ID, such as a driver's license or a utility bill.",
               "Por lo general, no. Si es la primera vez que votas en tu lugar de votación, lleva una identificación, como una licencia de conducir o un recibo de servicios."),
        'reg': ("Only through October 19, 2026, online, by mail, or in person. Pennsylvania has no Election Day registration.",
                "Solo hasta el 19 de octubre de 2026, en línea, por correo o en persona. Pennsylvania no tiene registro el Día de las Elecciones."),
    },
    'RI': {
        'polls': ("Polls are open 7 a.m. to 8 p.m. In New Shoreham, on Block Island, they open at 9 a.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m. En New Shoreham, en Block Island, abren a las 9 a. m."),
        'id': ("Bring a photo ID, such as a Rhode Island driver's license, a free voter ID card, or a U.S. passport. Without one, you vote a provisional ballot that counts if your signature matches.",
               "Lleva una identificación con foto, como una licencia de conducir de Rhode Island, una credencial gratuita de votante o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional que cuenta si tu firma coincide."),
        'reg': no_reg('Rhode Island', 'October 4, 2026', '4 de octubre de 2026'),
    },
    'SC': {
        'polls': ("Polling places are open 7 a.m. to 7 p.m. If you're in line at 7 p.m., you can vote.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. Si estás en la fila a las 7 p. m., puedes votar."),
        'id': ("You'll be asked for a photo ID, such as a South Carolina driver's license or a U.S. passport. If you can't get one, bring your voter registration card and sign an affidavit.",
               "Te pedirán una identificación con foto, como una licencia de conducir de South Carolina o un pasaporte de EE. UU. Si no puedes conseguir una, lleva tu tarjeta de registro de votante y firma una declaración jurada."),
        'reg': ("No, not for the November 3 election. Online registration closed October 4, 2026, and South Carolina has no same-day registration.",
                "No para la elección del 3 de noviembre. El registro en línea cerró el 4 de octubre de 2026, y South Carolina no tiene registro el mismo día."),
        'selfie': ("No. South Carolina law makes it unlawful to let anyone see your ballot.",
                   "No. En South Carolina es ilegal dejar que alguien vea tu boleta."),
    },
    'SD': {
        'polls': ("Polls are open 7 a.m. to 7 p.m. local time. South Dakota has two time zones.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m., hora local. South Dakota tiene dos zonas horarias."),
        'id': ("You'll be asked for a photo ID, such as a South Dakota driver's license. Without one, you can sign an affidavit and still vote a regular ballot.",
               "Te pedirán una identificación con foto, como una licencia de conducir de South Dakota. Si no tienes una, puedes firmar una declaración jurada y votar con una boleta regular."),
        'reg': ("Only through October 19, 2026. Your signed form must reach your county auditor by then, and South Dakota has no Election Day registration.",
                "Solo hasta el 19 de octubre de 2026. Tu formulario firmado debe llegar al auditor de tu condado (county auditor) a más tardar ese día, y South Dakota no tiene registro el Día de las Elecciones."),
        'selfie': ("No. South Dakota law bars making a marked ballot public in a way that reveals the votes on it, and a photo you share would do that.",
                   "No. La ley de South Dakota prohíbe hacer pública una boleta marcada de una forma que revele sus votos, y una foto que compartas lo haría."),
    },
    'TN': {
        'polls': ("Opening times vary by county. All polls close at 8 p.m. Eastern or 7 p.m. Central time.",
                  "La hora de apertura cambia según el condado. Todos los lugares de votación cierran a las 8 p. m., hora del Este, o a las 7 p. m., hora del Centro."),
        'id': ("Yes. Show a federal or Tennessee photo ID, such as a Tennessee driver's license, a U.S. passport, or a military ID.",
               "Sí. Muestra una identificación con foto federal o de Tennessee, como una licencia de conducir de Tennessee, un pasaporte de EE. UU. o una identificación militar."),
        'reg': no_reg('Tennessee', 'October 5, 2026', '5 de octubre de 2026'),
        'selfie': ("Not at the polls. Tennessee law lets you use your phone in the polling place for voting information, but not to take photos or video.",
                   "No en el lugar de votación. La ley de Tennessee te permite usar tu celular en el lugar de votación para consultar información sobre tu voto, pero no para tomar fotos ni video."),
    },
    'TX': {
        'polls': ("Polls are open 7 a.m. to 7 p.m. local time. Texas has two time zones.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m., hora local. Texas tiene dos zonas horarias."),
        'id': ("Show one of seven accepted photo IDs, such as a Texas driver's license or a U.S. passport. If you can't reasonably get one, you can sign a Reasonable Impediment Declaration and show supporting ID, such as your voter registration certificate.",
               "Muestra una de las siete identificaciones con foto aceptadas, como una licencia de conducir de Texas o un pasaporte de EE. UU. Si no puedes conseguir una por una razón válida, puedes firmar una declaración de impedimento razonable (Reasonable Impediment Declaration) y mostrar un documento de apoyo, como tu certificado de registro de votante."),
        'reg': no_reg('Texas', 'October 5, 2026', '5 de octubre de 2026'),
        'selfie': ("Not at the polls. Texas law bars recording images within 100 feet of a voting station.",
                   "No en el lugar de votación. La ley de Texas prohíbe grabar imágenes a menos de 100 pies de una cabina de votación."),
    },
    'UT': {
        'polls': ("Polling places are open 7 a.m. to 8 p.m. Utah also mails ballots to voters. Your mail ballot must reach the county clerk by 8 p.m. on November 3, and a postmark isn't enough.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m. Utah también envía boletas por correo a los votantes. Tu boleta por correo debe llegar a la oficina del secretario del condado (county clerk) a más tardar a las 8 p. m. del 3 de noviembre, y el matasellos no basta."),
        'id': ("Yes, to vote in person. Show one photo ID, such as a Utah driver's license or a U.S. passport, or two documents that together prove your name and address.",
               "Sí, para votar en persona. Muestra una identificación con foto, como una licencia de conducir de Utah o un pasaporte de EE. UU., o dos documentos que juntos comprueben tu nombre y tu dirección."),
        'reg': ("Yes. Your registration must reach your county clerk by 5 p.m. on October 23, 2026. After that, you can register in person during early voting or on Election Day with two forms of ID.",
                "Sí. Tu registro debe llegar a la oficina del secretario de tu condado a más tardar a las 5 p. m. del 23 de octubre de 2026. Después, puedes registrarte en persona durante la votación anticipada o el Día de las Elecciones con dos formas de identificación."),
        'selfie': ("Yes. Utah law lets you photograph your own ballot and share it. Photographing someone else's ballot at a polling place is a misdemeanor.",
                   "Sí. La ley de Utah te permite tomarle foto a tu propia boleta y compartirla. Tomarle foto a la boleta de otra persona en un lugar de votación es un delito menor."),
    },
    'VT': {
        'polls': ("Polls open between 5 a.m. and 10 a.m., depending on the town, and close at 7 p.m. Vermont also mails a ballot to every active voter. Return it to your town clerk by the day before the election, or to your polling place by 7 p.m.",
                  "Los lugares de votación abren entre las 5 a. m. y las 10 a. m., según el municipio, y cierran a las 7 p. m. Vermont también envía una boleta por correo a cada votante activo. Devuélvela al secretario de tu municipio (town clerk) a más tardar el día antes de la elección, o a tu lugar de votación antes de las 7 p. m."),
        'id': ("Usually not. If you registered for the first time by mail or online, you must include a copy of an ID, such as a driver's license or utility bill, with your application.",
               "Por lo general, no. Si te registraste por primera vez por correo o en línea, debes incluir una copia de una identificación, como una licencia de conducir o un recibo de servicios, con tu solicitud."),
        'reg': ("Yes. You can register at your town or city clerk's office during business hours before the election, or at your polling place on Election Day.",
                "Sí. Puedes registrarte en la oficina del secretario de tu municipio o ciudad en horario de oficina antes de la elección, o en tu lugar de votación el Día de las Elecciones."),
    },
    'VA': {
        'polls': ("Polling places are open 6 a.m. to 7 p.m. If you're in line at 7 p.m., you can vote.",
                  "Los lugares de votación abren de 6 a. m. a 7 p. m. Si estás en la fila a las 7 p. m., puedes votar."),
        'id': ("Show an acceptable ID, which doesn't need a photo. If you don't have one, you can sign an ID Confirmation Statement and vote a regular ballot.",
               "Muestra una identificación aceptada, que no necesita tener foto. Si no tienes una, puedes firmar una declaración de confirmación de identidad (ID Confirmation Statement) y votar con una boleta regular."),
        'reg': ("Yes. Register by October 23, 2026. After that, you can register in person and vote a provisional ballot through Election Day.",
                "Sí. Regístrate a más tardar el 23 de octubre de 2026. Después, puedes registrarte en persona y votar con una boleta provisional hasta el Día de las Elecciones."),
    },
    'WA': {
        'mail': ("Washington votes by mail. Put your ballot in an official drop box by 8 p.m. on Election Day, or mail it with a postmark dated Election Day or earlier. The state recommends mailing it a week early.",
                 "Washington vota por correo. Deja tu boleta en un buzón de entrega oficial a más tardar a las 8 p. m. del Día de las Elecciones, o envíala con matasellos del Día de las Elecciones o antes. El estado recomienda enviarla con una semana de anticipación."),
        'id': ("No. Washington votes by mail and checks your signature. At a voting center, staff check your signature or ID before you use an accessible voting unit.",
               "No. Washington vota por correo y revisa tu firma. En un centro de votación, el personal revisa tu firma o tu identificación antes de que uses un equipo de votación accesible."),
        'reg': ("Yes. Online and mail registrations must arrive by October 26, 2026, and you can register in person until 8 p.m. on Election Day.",
                "Sí. Los registros en línea y por correo deben llegar a más tardar el 26 de octubre de 2026, y puedes registrarte en persona hasta las 8 p. m. del Día de las Elecciones."),
        'selfie': ("Yes. Washington doesn't prohibit ballot selfies. It's illegal to view someone else's ballot for a purpose such as vote buying.",
                   "Sí. Washington no prohíbe las selfies con la boleta. Es ilegal ver la boleta de otra persona con un fin como comprar votos."),
    },
    'WV': {
        'polls': ("Polls are open 6:30 a.m. to 7:30 p.m. at your assigned polling place.",
                  "Los lugares de votación abren de 6:30 a. m. a 7:30 p. m. en el lugar que te corresponde."),
        'id': ("Yes. Show an unexpired photo ID, such as a West Virginia driver's license, a U.S. passport, or a West Virginia student ID. Without one, you vote a provisional ballot.",
               "Sí. Muestra una identificación vigente con foto, como una licencia de conducir de West Virginia, un pasaporte de EE. UU. o una identificación de estudiante de West Virginia. Si no tienes una, votas con una boleta provisional."),
        'reg': no_reg('West Virginia', 'October 13, 2026', '13 de octubre de 2026'),
        'selfie': ("Not in the voting booth. West Virginia law bars taking a recording device into the booth to record voting.",
                   "No en la cabina de votación. La ley de West Virginia prohíbe entrar a la cabina con un aparato de grabación para grabar la votación."),
    },
    'WI': {
        'polls': ("Polls are open 7 a.m. to 8 p.m. Absentee ballots must reach your municipal clerk by 8 p.m. on Election Day.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m. Las boletas de voto en ausencia deben llegar al secretario de tu municipio (municipal clerk) a más tardar a las 8 p. m. del Día de las Elecciones."),
        'id': ("Yes. Show an acceptable photo ID, such as a Wisconsin driver's license, a free state ID card, or a U.S. passport. Without one, you vote a provisional ballot and must show ID afterward.",
               "Sí. Muestra una identificación con foto aceptada, como una licencia de conducir de Wisconsin, una tarjeta de identificación estatal gratuita o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional y debes mostrar tu identificación después."),
        'reg': ("Yes. Online and mail registration closed October 14, 2026. You can register at your clerk's office until 5 p.m. on October 30, or at your polling place on Election Day. Bring proof of where you live.",
                "Sí. El registro en línea y por correo cerró el 14 de octubre de 2026. Puedes registrarte en la oficina del secretario municipal hasta las 5 p. m. del 30 de octubre, o en tu lugar de votación el Día de las Elecciones. Lleva un comprobante de domicilio."),
        'selfie': ("No. Wisconsin law makes it an offense to show your marked ballot to anyone, and a photo you share would do that.",
                   "No. En Wisconsin es un delito mostrarle tu boleta marcada a cualquier persona, y una foto que compartas lo haría."),
    },
    'WY': {
        'polls': ("Polls are open 7 a.m. to 7 p.m. If you're in line at 7 p.m., you can still vote.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. Si estás en la fila a las 7 p. m., todavía puedes votar."),
        'id': ("Yes. Show an acceptable ID, such as a Wyoming driver's license or ID card, a U.S. passport, a tribal ID, or a Medicare or Medicaid card.",
               "Sí. Muestra una identificación aceptada, como una licencia de conducir o tarjeta de identificación de Wyoming, un pasaporte de EE. UU., una identificación tribal o una tarjeta de Medicare o Medicaid."),
        'reg': ("Yes. Regular registration closes October 20, 2026, but you can register and vote at the same time during early absentee voting or on Election Day. Bring proof of identity, citizenship, and residence.",
                "Sí. El registro regular cierra el 20 de octubre de 2026, pero puedes registrarte y votar al mismo tiempo durante la votación anticipada en ausencia o el Día de las Elecciones. Lleva comprobantes de identidad, ciudadanía y domicilio."),
    },
})

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
