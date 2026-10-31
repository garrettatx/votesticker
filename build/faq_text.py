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
        'id': ("Bring a photo ID, such as an Alabama driver's license, a U.S. passport, or a free Alabama Photo Voter ID card. It must be the physical card, not a digital ID. Without one, you can still vote a regular ballot if two election officials identify you.",
               'Lleva una identificación con foto, como una licencia de conducir de Alabama, un pasaporte de EE. UU. o la credencial gratuita Alabama Photo Voter ID. Tiene que ser la tarjeta física, no una identificación digital. Si no tienes una, todavía puedes votar con una boleta regular si dos funcionarios electorales te identifican.'),
        'reg': ("No, not for the November 3 election. The deadline was October 19, 2026, and Alabama has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 19 de octubre de 2026, y Alabama no tiene registro el Día de las Elecciones."),
        'selfie': ("Yes, you can take a photo of your own ballot. Other photos inside the polling place are not allowed.",
                   "Sí, puedes tomarle foto a tu propia boleta. No se permiten otras fotos dentro del lugar de votación."),
    },
    'AK': {
        'polls': ("Polls are open 7 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m."),
        'id': ("Yes, but it doesn't have to be a photo ID. Show one ID, such as your voter card, a driver's license or a passport, or a current utility bill or bank statement with your name and address. A poll worker who knows you can waive it, except for some first-time voters who registered by mail or online.",
               'Sí, pero no tiene que ser una identificación con foto. Muestra una identificación, como tu tarjeta de votante, una licencia de conducir o un pasaporte, o un recibo de servicios o un estado de cuenta bancario reciente con tu nombre y dirección. Un trabajador electoral que te conozca puede dispensarla, salvo para algunos votantes nuevos que se registraron por correo o en línea.'),
        'reg': ("No, not for the November 3 election. The deadline was October 4, 2026, and Alaska has no Election Day registration for state and federal races.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 4 de octubre de 2026, y Alaska no tiene registro el Día de las Elecciones para las elecciones estatales y federales."),
        'selfie': ("Don't share one. Alaska law bars showing your marked ballot to anyone so they can tell how you voted, and posting a photo of it would do that. Share a sticker instead.",
                   'No la compartas. La ley de Alaska prohíbe mostrar tu boleta marcada a cualquier persona para que sepa cómo votaste, y publicar una foto de ella lo haría. Mejor comparte un sticker.'),
    },
    'AZ': {
        'polls': ("Polls are open 6 a.m. to 7 p.m.",
                  "Los lugares de votación abren de 6 a. m. a 7 p. m."),
        'id': ('Yes. Show one photo ID with your name and address, such as an Arizona driver license or a tribal ID, or two documents such as a utility bill and a bank statement. Without acceptable ID, you vote a conditional provisional ballot and have until Sunday, November 8, to show ID to your county recorder.',
               'Sí. Muestra una identificación con foto que tenga tu nombre y dirección, como una licencia de conducir de Arizona o una identificación tribal, o dos documentos, como un recibo de servicios y un estado de cuenta bancario. Si no tienes una identificación aceptada, votas con una boleta provisional condicional y tienes hasta el domingo 8 de noviembre para mostrar tu identificación al registrador de tu condado (county recorder).'),
        'reg': ("No, not for the November 3 election. The deadline was October 5, 2026, and Arizona has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 5 de octubre de 2026, y Arizona no tiene registro el Día de las Elecciones."),
        'selfie': ('Not at the polls. No one may take photos or video within the marked 75-foot limit of a voting location. Arizona allows posting a photo of your own ballot, so a picture of your mail ballot is fine.',
                   'No en el lugar de votación. Nadie puede tomar fotos ni video dentro del límite marcado de 75 pies de un lugar de votación. Arizona permite publicar una foto de tu propia boleta, así que una foto de tu boleta por correo está bien.'),
    },
    'AR': {
        'polls': ("Polls are open 7:30 a.m. to 7:30 p.m.",
                  "Los lugares de votación abren de 7:30 a. m. a 7:30 p. m."),
        'id': ("Yes. Show a photo ID, such as an Arkansas driver's license (the Arkansas Mobile ID app works too), a U.S. passport, or a concealed carry license. Without one, you vote a provisional ballot and have until noon on Monday, November 9, to show ID to your county clerk or county election commission.",
               'Sí. Muestra una identificación con foto, como una licencia de conducir de Arkansas (también sirve la app Arkansas Mobile ID), un pasaporte de EE. UU. o una licencia para portar armas ocultas (concealed carry license). Si no tienes una, votas con una boleta provisional y tienes hasta el mediodía del lunes 9 de noviembre para mostrar tu identificación al secretario de tu condado (county clerk) o a la comisión electoral del condado.'),
        'reg': ("No, not for the November 3 election. The deadline was October 5, 2026, and Arkansas has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 5 de octubre de 2026, y Arkansas no tiene registro el Día de las Elecciones."),
    },
    'CA': {
        'mail': ('Every active registered voter gets a ballot by mail. Mail it postmarked by November 3 so it arrives within seven days, or drop it off by 8 p.m. that day. Polling places and vote centers are open 7 a.m. to 8 p.m. on November 3.',
                 'Todo votante registrado activo recibe una boleta por correo. Envíala con matasellos del 3 de noviembre o antes para que llegue en un plazo de siete días, o entrégala a más tardar a las 8 p. m. de ese día. Los lugares y centros de votación abren de 7 a. m. a 8 p. m. el 3 de noviembre.'),
        'id': ("Usually not. Most California voters don't show ID. Some first-time voters who registered without an ID number may be asked for one, such as a driver's license, passport, or utility bill.",
               "Por lo general, no. La mayoría de los votantes de California no muestran identificación. A algunos votantes nuevos que se registraron sin un número de identificación se les puede pedir una, como una licencia de conducir, un pasaporte o un recibo de servicios."),
        'reg': ("Yes. The regular deadline was October 19, 2026. You can still register and vote the same day at your county elections office, a vote center, or a polling place, through Election Day.",
                "Sí. La fecha límite regular fue el 19 de octubre de 2026. Todavía puedes registrarte y votar el mismo día en la oficina electoral de tu condado, en un centro de votación o en un lugar de votación, hasta el Día de las Elecciones."),
        'selfie': ("Yes. Since 2017, California law lets you photograph your own ballot and share it. You can't photograph other voters going in or out.",
                   "Sí. Desde 2017, la ley de California te permite tomarle foto a tu propia boleta y compartirla. No puedes tomar fotos de otros votantes al entrar o salir."),
    },
    'CO': {
        'mail': ("Colorado votes by mail. Your ballot must reach your county clerk by 7 p.m. on November 3, and postmarks don't count. Voter service and polling centers are open for in-person voting until 7 p.m. that day.",
                 "Colorado vota por correo. Tu boleta debe llegar a la oficina electoral de tu condado (county clerk) a más tardar a las 7 p. m. del 3 de noviembre, y el matasellos no cuenta. Los centros de votación y servicio al votante están abiertos para votar en persona hasta las 7 p. m. de ese día."),
        'id': ("If you vote in person, yes, but it doesn't have to be a photo ID. Most people bring a Colorado driver's license or ID card. If your ID shows an address, it must be in Colorado, but it doesn't have to match your registration.",
               'Si votas en persona, sí, pero no tiene que ser una identificación con foto. La mayoría lleva una licencia de conducir o una identificación de Colorado. Si tu identificación muestra una dirección, tiene que ser de Colorado, pero no tiene que coincidir con tu registro.'),
        'reg': ("Yes. Register online or by mail by October 26, 2026 to get a ballot by mail. After that, you can register and vote in person at a voter service and polling center through Election Day.",
                "Sí. Regístrate en línea o por correo a más tardar el 26 de octubre de 2026 para recibir una boleta por correo. Después, puedes registrarte y votar en persona en un centro de votación y servicio al votante hasta el Día de las Elecciones.",
                # After the date passes (October 25): what the page says instead.
                {'until': '2026-10-26', 'en': "Yes, in person. Online and mail registration to get a ballot by mail closed October 26, 2026. You can still register and vote in person at a voter service and polling center through Election Day.",
                 'es': "Sí, en persona. El registro en línea y por correo para recibir una boleta por correo cerró el 26 de octubre de 2026. Todavía puedes registrarte y votar en persona en un centro de votación y servicio al votante hasta el Día de las Elecciones."}),
        'selfie': ("Yes. A 2017 law lets you show your voted ballot to others, including in a photo. County clerks can set reasonable limits on photos at voting centers.",
                   "Sí. Una ley de 2017 te permite mostrar tu boleta votada a otras personas, también en una foto. Las oficinas electorales de los condados pueden poner límites razonables a las fotos en los centros de votación."),
    },
    'CT': {
        'polls': ("Polls are open 6 a.m. to 8 p.m. If you're in line by 8 p.m., you can vote.",
                  "Los lugares de votación abren de 6 a. m. a 8 p. m. Si estás en la fila a las 8 p. m., puedes votar."),
        'id': ("You'll be asked for ID, such as a Social Security card or another preprinted ID with your name and your address, signature, or photo. If you don't have one, you can sign a statement instead.",
               "Te pedirán una identificación, como tu tarjeta del Seguro Social u otra identificación impresa con tu nombre y tu dirección, firma o foto. Si no tienes una, puedes firmar una declaración en su lugar."),
        'reg': ("Yes. The regular deadline was October 16, 2026, but you can register and vote the same day during early voting, October 19 to November 1, or on Election Day at your town's Same Day Registration location. Bring a birth certificate, driver's license or Social Security card, plus proof of your address in town if it isn't on that ID.",
                'Sí. La fecha límite regular fue el 16 de octubre de 2026, pero puedes registrarte y votar el mismo día durante la votación anticipada, del 19 de octubre al 1 de noviembre, o el Día de las Elecciones en el lugar de registro el mismo día (Same Day Registration) de tu municipio. Lleva tu acta de nacimiento, licencia de conducir o tarjeta del Seguro Social, y un comprobante de tu dirección en el municipio si no aparece en esa identificación.',
                {'until': '2026-11-01', 'en': "Yes, on Election Day. The regular deadline was October 16, 2026, but you can register and vote the same day at your town's Same Day Registration location. Bring a birth certificate, driver's license or Social Security card, plus proof of your address in town if it isn't on that ID.",
                 'es': 'Sí, el Día de las Elecciones. La fecha límite regular fue el 16 de octubre de 2026, pero puedes registrarte y votar el mismo día en el lugar de registro el mismo día (Same Day Registration) de tu municipio. Lleva tu acta de nacimiento, licencia de conducir o tarjeta del Seguro Social, y un comprobante de tu dirección en el municipio si no aparece en esa identificación.'}),
    },
    'DE': {
        'polls': ("Polls are open 7 a.m. to 8 p.m., and you vote at your assigned polling place.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m., y votas en el lugar de votación que te corresponde."),
        'id': ("Not a photo ID. Poll workers ask for proof of identity, such as a Delaware driver's license or state ID, or a copy of a utility bill or bank statement with your name and address. If you don't have it with you, you can sign an affidavit and vote.",
               "No necesitas identificación con foto. Los trabajadores electorales piden un comprobante de identidad, como una licencia de conducir o identificación estatal de Delaware, o una copia de un recibo de servicios o estado de cuenta bancario con tu nombre y dirección. Si no lo traes contigo, puedes firmar una declaración jurada y votar."),
        'reg': ("No, not for the November 3 election. The deadline was October 10, 2026, and Delaware has no same-day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 10 de octubre de 2026, y Delaware no tiene registro el mismo día."),
    },
    'DC': {
        'polls': ("Vote centers are open 7 a.m. to 8 p.m. on November 3, and you can vote at any of them. If you're in line at 8 p.m., you can still vote.",
                  'Los centros de votación abren de 7 a. m. a 8 p. m. el 3 de noviembre, y puedes votar en cualquiera. Si estás en la fila a las 8 p. m., todavía puedes votar.'),
        'id': ("Most registered voters don't need ID. If you're voting in a federal election in DC for the first time and registered by mail, you may need a photo ID or a document with your name and address, such as a utility bill, unless you sent one when you registered.",
               'La mayoría de los votantes registrados no necesitan identificación. Si votas por primera vez en una elección federal en DC y te registraste por correo, es posible que necesites una identificación con foto o un documento con tu nombre y dirección, como un recibo de servicios, salvo que ya lo hayas enviado al registrarte.'),
        'reg': ('Yes. Online and mail registration closed October 13, 2026, but you can register and vote the same day at any Vote Center during early voting or on Election Day. Bring proof of your name and current DC address.',
                'Sí. El registro en línea y por correo cerró el 13 de octubre de 2026, pero puedes registrarte y votar el mismo día en cualquier centro de votación durante la votación anticipada o el Día de las Elecciones. Lleva un comprobante con tu nombre y tu dirección actual en DC.'),
    },
    'FL': {
        'polls': ("Polls are open 7 a.m. to 7 p.m., on the time used in your county seat. In Gulf County that's Eastern time, so polls in its Central time area are open 6 a.m. to 6 p.m. Central.",
                  'Los lugares de votación abren de 7 a. m. a 7 p. m., según la hora de la cabecera de tu condado. En el condado de Gulf es la hora del Este, así que en su parte con hora del Centro abren de 6 a. m. a 6 p. m., hora del Centro.'),
        'id': ('Yes. Show a current photo ID with your signature, such as a Florida driver license, a Florida ID card, or a U.S. passport. Without one, you vote a provisional ballot. Student IDs and debit or credit cards with your photo still count on November 3.',
               'Sí. Muestra una identificación vigente con foto y tu firma, como una licencia de conducir de Florida, una tarjeta de identificación de Florida o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional. Las identificaciones de estudiante y las tarjetas de débito o crédito con tu foto todavía sirven el 3 de noviembre.'),
        'reg': ('Not for most voters. The deadline was October 5, 2026, and Florida has no Election Day registration. If you just left the military or a job abroad, you and family who came with you can register at your Supervisor of Elections office until 5 p.m. on October 30.',
                'No para la mayoría de los votantes. La fecha límite fue el 5 de octubre de 2026, y Florida no tiene registro el Día de las Elecciones. Si acabas de dejar el servicio militar o un trabajo en el extranjero, tú y los familiares que vinieron contigo pueden registrarse en la oficina del supervisor electoral (Supervisor of Elections) hasta las 5 p. m. del 30 de octubre.',
                {'until': '2026-10-30', 'en': 'No, not for the November 3 election. The deadline was October 5, 2026, and Florida has no Election Day registration.',
                 'es': 'No para la elección del 3 de noviembre. La fecha límite fue el 5 de octubre de 2026, y Florida no tiene registro el Día de las Elecciones.'}),
        'selfie': ("Yes, you can photograph your own ballot. No other photos are allowed in the polling room or early voting area.",
                   "Sí, puedes tomarle foto a tu propia boleta. No se permiten otras fotos en la sala de votación ni en el área de votación anticipada."),
    },
    'MT': {
        'polls': ("Most polling places are open 7 a.m. to 8 p.m. A polling place with fewer than 400 voters may open as late as noon, and the county mails affected voters a notice the first time it changes the hours.",
                  "La mayoría de los lugares de votación abren de 7 a. m. a 8 p. m. Un lugar de votación con menos de 400 votantes puede abrir hasta el mediodía, y el condado envía un aviso por correo a los votantes afectados la primera vez que cambia el horario."),
        'id': ("Yes. Show a photo ID such as a Montana driver's license, a state ID card, a U.S. passport, or a tribal photo ID. Another photo ID works if you also bring a utility bill or bank statement with your name and address.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir de Montana, una tarjeta de identificación estatal, un pasaporte de EE. UU. o una identificación tribal con foto. Otra identificación con foto sirve si también llevas un recibo de servicios o un estado de cuenta bancario con tu nombre y dirección."),
        'reg': ("Yes, in person. Regular registration closed October 5, 2026. You can still register at your county election office through 8 p.m. on Election Day. Late registration pauses at noon on Monday, November 2, and reopens on Election Day.",
                "Sí, en persona. El registro regular cerró el 5 de octubre de 2026. Todavía puedes registrarte en la oficina electoral de tu condado hasta las 8 p. m. del Día de las Elecciones. El registro tardío se pausa al mediodía del lunes 2 de noviembre y vuelve a abrir el Día de las Elecciones."),
        'selfie': ("Yes. Montana's Commissioner of Political Practices says a selfie with your own marked ballot, shared with friends or on social media, doesn't break state law, as long as you don't disrupt voting.",
                   "Sí. El Comisionado de Prácticas Políticas de Montana (Commissioner of Political Practices) dice que una selfie con tu propia boleta marcada, compartida con amigos o en redes sociales, no viola la ley estatal, siempre que no interrumpas la votación."),
    },
    'NE': {
        'polls': ("Polls are open 8 a.m. to 8 p.m. in the Central Time Zone and 7 a.m. to 7 p.m. in the Mountain Time Zone.",
                  "Los lugares de votación abren de 8 a. m. a 8 p. m. en la zona horaria del Centro y de 7 a. m. a 7 p. m. en la zona horaria de la Montaña."),
        'id': ("Yes. Show a photo ID with your name and photo, such as a Nebraska driver's license, a state ID, or a U.S. passport. It can be expired. Without one, you vote a provisional ballot and show your ID at your county election office by November 10.",
               'Sí. Muestra una identificación con tu nombre y foto, como una licencia de conducir de Nebraska, una identificación estatal o un pasaporte de EE. UU. Puede estar vencida. Si no tienes una, votas con una boleta provisional y muestras tu identificación en la oficina electoral de tu condado a más tardar el 10 de noviembre.'),
        'reg': ('No, not for most voters. Registration for the November 3 election closed at 6 p.m. on October 23, 2026. If you became a U.S. citizen after that, you can register and vote at your county election office until 7 p.m. Central (6 p.m. Mountain) on Election Day.',
                'No para la mayoría de los votantes. El registro para la elección del 3 de noviembre cerró a las 6 p. m. del 23 de octubre de 2026. Si obtuviste la ciudadanía de EE. UU. después de esa fecha, puedes registrarte y votar en la oficina electoral de tu condado hasta las 7 p. m., hora del Centro (6 p. m., hora de la Montaña), el Día de las Elecciones.'),
        'selfie': ("Yes. Nebraska law lets you photograph your own marked ballot and share the photo. No one may pressure you to show your ballot.",
                   "Sí. La ley de Nebraska te permite tomarle foto a tu propia boleta marcada y compartir la foto. Nadie puede presionarte para que muestres tu boleta."),
    },
    'NV': {
        'polls': ("Polling places are open 7 a.m. to 7 p.m. Nevada also mails a ballot to every active voter. Mail it postmarked by Election Day so it arrives by 5 p.m. on November 7, or drop it off before polls close.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. Nevada también envía una boleta por correo a cada votante activo. Envíala con matasellos del Día de las Elecciones o antes para que llegue a más tardar a las 5 p. m. del 7 de noviembre, o entrégala antes de que cierren los lugares de votación."),
        'id': ("Usually not; most voters sign to vote. Some first-time voters who registered by mail or online may need a photo ID, a utility bill, or a bank statement. Registering at the polls? Bring a current Nevada driver's license or DMV ID card.",
               'Por lo general, no; la mayoría de los votantes firman para votar. Algunos votantes nuevos que se registraron por correo o en línea podrían necesitar una identificación con foto, un recibo de servicios o un estado de cuenta bancario. ¿Te vas a registrar en el lugar de votación? Lleva una licencia de conducir o una tarjeta de identificación vigente del DMV de Nevada.'),
        'reg': ('Yes. You can register online through Election Day, or register and vote in person during early voting or on Election Day. Mail registration closed October 6, 2026. If you register or update online on or after October 21, you vote in person.',
                'Sí. Puedes registrarte en línea hasta el Día de las Elecciones, o registrarte y votar en persona durante la votación anticipada o el Día de las Elecciones. El registro por correo cerró el 6 de octubre de 2026. Si te registras o actualizas tu registro en línea el 21 de octubre o después, votas en persona.'),
        'selfie': ('Not at the polls. Nevada law bars the public from photographing voting at a polling place. The law does let you show your own mail ballot to others.',
                   'No en el lugar de votación. La ley de Nevada prohíbe que el público tome fotos de la votación en un lugar de votación. La ley sí te permite mostrarles a otros tu propia boleta por correo.'),
    },
    'NH': {
        'polls': ("Hours vary by town and city. Polls open by 11 a.m. and close no earlier than 7 p.m., and many open earlier. Your town or city clerk has the exact hours.",
                  "El horario cambia según el municipio o la ciudad. Los lugares de votación abren a más tardar a las 11 a. m. y cierran a las 7 p. m. o después, y muchos abren antes. El secretario de tu municipio o ciudad (town or city clerk) tiene el horario exacto."),
        'id': ("Yes. Show a valid photo ID, such as a driver's license from any state, a state ID card, a U.S. military ID, or a U.S. passport. Without one, an election official must confirm who you are before you can vote. Student IDs aren't accepted for this election.",
               "Sí. Muestra una identificación válida con foto, como una licencia de conducir de cualquier estado, una tarjeta de identificación estatal, una identificación militar de EE. UU. o un pasaporte de EE. UU. Si no tienes una, un funcionario electoral debe confirmar quién eres antes de que puedas votar. Las identificaciones de estudiante no se aceptan en esta elección."),
        'reg': ("Yes. Register at your polling place on Election Day, or with your town or city clerk until 6 to 13 days before the election (the date varies by town). Bring proof of identity, age, U.S. citizenship and domicile.",
                "Sí. Regístrate en tu lugar de votación el Día de las Elecciones, o con el secretario de tu municipio o ciudad (town or city clerk) hasta 6 a 13 días antes de la elección (la fecha cambia según el municipio). Lleva comprobantes de identidad, edad, ciudadanía estadounidense y domicilio."),
        'selfie': ("Yes, your own ballot. A federal appeals court struck down New Hampshire's ballot selfie ban in 2016. Don't photograph other voters or their ballots.",
                   "Sí, tu propia boleta. Una corte federal de apelaciones anuló en 2016 la prohibición de New Hampshire de tomarse selfies con la boleta. No tomes fotos de otros votantes ni de sus boletas."),
    },
    'NJ': {
        'polls': ("Polls are open 6 a.m. to 8 p.m. In-person early voting runs October 24 to November 1.",
                  "Los lugares de votación abren de 6 a. m. a 8 p. m. La votación anticipada en persona es del 24 de octubre al 1 de noviembre.",
                {'until': '2026-11-01', 'en': 'Polls are open 6 a.m. to 8 p.m.',
                 'es': 'Los lugares de votación abren de 6 a. m. a 8 p. m.'}),
        'id': ('Usually not. You show ID only if you\'re a "Need ID" voter, for example if you registered by mail and haven\'t shown ID yet, or your driver\'s license or Social Security number couldn\'t be verified. A current photo ID or a document with your name and address, such as a utility bill, works. Without one, you vote a provisional ballot.',
               'Por lo general, no. Solo muestras identificación si eres un votante "Need ID", por ejemplo, si te registraste por correo y todavía no has mostrado identificación, o si no se pudo verificar tu número de licencia de conducir o de Seguro Social. Sirve una identificación con foto vigente o un documento con tu nombre y dirección, como un recibo de servicios. Si no tienes una, votas con una boleta provisional.'),
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
        'selfie': ("Yes, for your own ballot. New Mexico law lets you share how you marked your ballot in any form you choose. Inside the polling place, don't show your marked ballot to anyone, and ask a poll worker before taking other photos.",
                   'Sí, de tu propia boleta. La ley de New Mexico te permite compartir cómo marcaste tu boleta en la forma que elijas. Dentro del lugar de votación, no le muestres tu boleta marcada a nadie, y pregunta a un trabajador electoral antes de tomar otras fotos.'),
    },
    'NY': {
        'polls': ("Polls are open 6 a.m. to 9 p.m. Early voting runs October 24 to November 1, with hours set by each county.",
                  "Los lugares de votación abren de 6 a. m. a 9 p. m. La votación anticipada es del 24 de octubre al 1 de noviembre, con horarios que fija cada condado.",
                {'until': '2026-11-01', 'en': 'Polls are open 6 a.m. to 9 p.m.',
                 'es': 'Los lugares de votación abren de 6 a. m. a 9 p. m.'}),
        'id': ("Usually not; you sign the poll book. Some first-time voters who registered by mail may need a photo ID, a utility bill, a bank statement, or a paycheck.",
               "Por lo general, no; firmas el registro de votantes. Algunos votantes nuevos que se registraron por correo podrían necesitar una identificación con foto, un recibo de servicios, un estado de cuenta bancario o un talón de pago."),
        'reg': ("No, not for the November 3 election. Your application had to reach your county board of elections by October 24, 2026, and New York has no Election Day registration.",
                "No para la elección del 3 de noviembre. Tu solicitud tenía que llegar a la junta electoral de tu condado (county board of elections) a más tardar el 24 de octubre de 2026, y New York no tiene registro el Día de las Elecciones."),
        'selfie': ("No. New York law makes it a misdemeanor to show your marked ballot to anyone in a way that reveals your votes, and that includes a ballot selfie.",
                   "No. En New York es un delito menor mostrar tu boleta marcada a cualquier persona de una forma que revele tus votos, y eso incluye una selfie con tu boleta."),
    },
    'NC': {
        'polls': ("Polling places are open 6:30 a.m. to 7:30 p.m. If you're in line at 7:30 p.m., you can still vote.",
                  "Los lugares de votación abren de 6:30 a. m. a 7:30 p. m. Si estás en la fila a las 7:30 p. m., todavía puedes votar."),
        'id': ("You'll be asked for a photo ID, such as a North Carolina driver's license, a U.S. passport, or a free voter photo ID card. If you can't show one, you can still vote a provisional ballot by filling out an ID Exception Form. It counts if you fill out the form truthfully.",
               'Te pedirán una identificación con foto, como una licencia de conducir de North Carolina, un pasaporte de EE. UU. o una credencial gratuita de votante con foto. Si no puedes mostrar una, todavía puedes votar con una boleta provisional si llenas un formulario de excepción de identificación (ID Exception Form). Cuenta si llenas el formulario con la verdad.'),
        'reg': ("Yes, during early voting, until 3 p.m. on Saturday, October 31. Register and vote at the same time at an early voting site in your county, and bring proof of your current address. You can't register on Election Day.",
                'Sí, durante la votación anticipada, hasta las 3 p. m. del sábado 31 de octubre. Regístrate y vota al mismo tiempo en un centro de votación anticipada de tu condado, y lleva un comprobante de tu dirección actual. No puedes registrarte el Día de las Elecciones.',
                {'until': '2026-10-31', 'en': "Not for most voters. Same-day registration ended with early voting on October 31, and you can't register on Election Day. The exception: if you became eligible after the October 9 deadline because you became a U.S. citizen or had your rights restored after a felony conviction, you can register on Election Day.",
                 'es': 'No para la mayoría de los votantes. El registro el mismo día terminó con la votación anticipada el 31 de octubre, y no puedes registrarte el Día de las Elecciones. La excepción: si cumpliste los requisitos después de la fecha límite del 9 de octubre porque obtuviste la ciudadanía de EE. UU. o te restituyeron tus derechos después de una condena por delito grave (felony), puedes registrarte el Día de las Elecciones.',
                 'src': 'https://www.ncsbe.gov/news/press-releases/2026/10/30/10-tips-election-day-voters-2026-general-election',
                 'quote': 'Voter registration is not permitted on Election Day. While that is the general rule, voters who become eligible after the regular voter registration deadline (Oct. 9), either due to becoming a U.S. citizen or having their rights restored following a felony conviction, are permitted to register on Election Day.'}),
        'selfie': ("No. North Carolina law bars photographing a voted ballot. It also bars photographing voters in the voting area without their permission and the chief judge's.",
                   "No. La ley de North Carolina prohíbe tomarle foto a una boleta votada. También prohíbe tomar fotos de los votantes en el área de votación sin su permiso y el del juez principal (chief judge)."),
    },
    'ND': {
        'polls': ("Hours vary by county or city. Polls open between 7 a.m. and 9 a.m. and close between 7 p.m. and 9 p.m. local time. North Dakota has two time zones.",
                  "El horario cambia según el condado o la ciudad. Los lugares de votación abren entre las 7 a. m. y las 9 a. m. y cierran entre las 7 p. m. y las 9 p. m., hora local. North Dakota tiene dos zonas horarias."),
        'id': ("Yes. Show a North Dakota driver's license, nondriver ID card, or tribal ID with your name, current North Dakota address, and birth date. A utility bill or bank statement can fill in missing details. No valid ID with you? You can still vote a set-aside ballot. It counts once you show your ID to the county auditor within 13 days, before the County Canvassing Board meets.",
               "Sí. Muestra una licencia de conducir de North Dakota, una tarjeta de identificación para no conductores o una identificación tribal con tu nombre, tu dirección actual en North Dakota y tu fecha de nacimiento. Un recibo de servicios o un estado de cuenta bancario puede completar los datos que falten. ¿No traes una identificación válida? Aun así puedes votar con una boleta apartada (set-aside ballot). Se cuenta cuando muestras tu identificación al auditor del condado dentro de los 13 días, antes de que se reúna la junta de escrutinio del condado (County Canvassing Board)."),
        'reg': ("You don't need to. North Dakota has no voter registration. If you're eligible, bring a valid ID with your current address and vote.",
                "No hace falta. North Dakota no tiene registro de votantes. Si cumples los requisitos, lleva una identificación válida con tu dirección actual y vota."),
    },
    'OH': {
        'polls': ("Polls are open 6:30 a.m. to 7:30 p.m., and you vote at your assigned polling place.",
                  "Los lugares de votación abren de 6:30 a. m. a 7:30 p. m., y votas en el lugar de votación que te corresponde."),
        'id': ("Yes. Show an unexpired photo ID, such as an Ohio driver's license, a state ID card, a U.S. passport, or a military ID. Without one, you vote a provisional ballot, and it counts only if you show an accepted ID at your county board of elections within four days after Election Day.",
               'Sí. Muestra una identificación vigente con foto, como una licencia de conducir de Ohio, una tarjeta de identificación estatal, un pasaporte de EE. UU. o una identificación militar. Si no tienes una, votas con una boleta provisional, y solo cuenta si muestras una identificación aceptada en la junta electoral de tu condado dentro de los cuatro días después del Día de las Elecciones.'),
        'reg': ("No, not for the November 3 election. The deadline was October 5, 2026, and Ohio has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 5 de octubre de 2026, y Ohio no tiene registro el Día de las Elecciones."),
        'selfie': ("Not to show how you voted. Ohio law makes it a felony to let someone see your ballot to reveal how you're about to vote. No official rule speaks directly to ballot photos, so ask a poll worker.",
                   'No para mostrar cómo votaste. La ley de Ohio castiga como delito grave dejar que alguien vea tu boleta para revelar cómo vas a votar. Ninguna regla oficial habla directamente de fotos de la boleta, así que pregunta a un trabajador electoral.'),
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
        'selfie': ('No. Georgia bans photos of ballots, including absentee ballots, and limits cameras and phones in the polling place while voting is going on. Take your sticker selfie outside the polling area.',
                   'No. Georgia prohíbe las fotos de boletas, incluidas las boletas de voto en ausencia, y limita el uso de cámaras y celulares en el lugar de votación mientras se está votando. Tómate la selfie con tu sticker fuera del área de votación.'),
    },
    'HI': {
        'mail': ("Hawaii votes by mail. Your ballot must be received by 7 p.m. on November 3, and a postmark isn't enough. Voter service centers are open 7 a.m. to 7 p.m. that day for in-person voting.",
                 "Hawaii vota por correo. Tu boleta debe recibirse a más tardar a las 7 p. m. del 3 de noviembre, y el matasellos no basta. Los centros de servicio al votante abren de 7 a. m. a 7 p. m. ese día para votar en persona."),
        'id': ("Bring an ID if you vote in person. In Honolulu, a driver's license, state ID or passport works, and so does a current utility bill, bank statement, paycheck or government document with your name and address. Without one, staff will ask you more questions to confirm who you are. That takes longer.",
               'Lleva una identificación si votas en persona. En Honolulu sirve una licencia de conducir, una identificación estatal o un pasaporte, y también un recibo de servicios, estado de cuenta bancario, talón de pago o documento del gobierno reciente con tu nombre y dirección. Si no tienes una, el personal te hará más preguntas para confirmar quién eres. Eso toma más tiempo.'),
        'reg': ("Yes. You can register and vote the same day at a voter service center, open October 20 through November 3.",
                "Sí. Puedes registrarte y votar el mismo día en un centro de servicio al votante, abierto del 20 de octubre al 3 de noviembre."),
        'selfie': ('Yes. Hawaii law lets you share a photo of your own marked ballot. Ask staff before taking photos inside a voter service center, and keep other voters out of the picture.',
                   'Sí. La ley de Hawaii te permite compartir una foto de tu propia boleta marcada. Pregunta al personal antes de tomar fotos dentro de un centro de servicio al votante, y no saques a otros votantes en la foto.'),
    },
    'ID': {
        'polls': ("Polls are open 8 a.m. to 8 p.m. local time, and some counties open at 7 a.m. Idaho has two time zones.",
                  "Los lugares de votación abren de 8 a. m. a 8 p. m., hora local, y algunos condados abren a las 7 a. m. Idaho tiene dos zonas horarias."),
        'id': ("You'll be asked for a photo ID, such as an Idaho driver's license, a U.S. passport, or a tribal ID. If you don't have one, you can sign an identity affidavit with your date of birth or Idaho license number, and you still vote a regular ballot.",
               'Te pedirán una identificación con foto, como una licencia de conducir de Idaho, un pasaporte de EE. UU. o una identificación tribal. Si no tienes una, puedes firmar una declaración jurada de identidad con tu fecha de nacimiento o tu número de licencia de Idaho, y aun así votas con una boleta regular.'),
        'reg': ("Yes. Preregistration closed October 23, 2026. You can still register and vote in person during early voting or on Election Day with a photo ID and proof of where you live.",
                "Sí. El registro previo cerró el 23 de octubre de 2026. Todavía puedes registrarte y votar en persona durante la votación anticipada o el Día de las Elecciones con una identificación con foto y un comprobante de domicilio."),
        'selfie': ("Idaho law doesn't address ballot photos. Ask a poll worker before taking pictures inside, and take your sticker selfie after you vote.",
                   'La ley de Idaho no dice nada sobre fotos de la boleta. Pregúntale a un trabajador electoral antes de tomar fotos adentro, y tómate la selfie con tu sticker después de votar.'),
    },
    'IL': {
        'polls': ("Polls are open 6 a.m. to 7 p.m.",
                  "Los lugares de votación abren de 6 a. m. a 7 p. m."),
        'id': ("Usually not; most voters give their name and sign in. If you first registered by mail and haven't proven your identity yet, you may need to show ID.",
               "Por lo general, no; la mayoría de los votantes dan su nombre y firman. Si te registraste por primera vez por correo y todavía no has comprobado tu identidad, es posible que tengas que mostrar una identificación."),
        'reg': ("Yes. Online registration closed October 18, 2026. You can still register and vote in person at grace period sites through Election Day.",
                "Sí. El registro en línea cerró el 18 de octubre de 2026. Todavía puedes registrarte y votar en persona en los lugares del periodo de gracia (grace period) hasta el Día de las Elecciones."),
        'selfie': ('We found no Illinois rule on ballot photos. The law makes it a felony to knowingly mark your ballot so someone else can see it, so keep it out of view and ask an election judge before taking photos inside.',
                   'No encontramos ninguna regla de Illinois sobre fotos de la boleta. La ley castiga como delito grave marcar a propósito tu boleta para que otra persona la vea, así que mantenla fuera de la vista y pregunta a un juez electoral antes de tomar fotos adentro.'),
    },
    'IN': {
        'polls': ("Polls are open 6 a.m. to 6 p.m. local time. Indiana has two time zones.",
                  "Los lugares de votación abren de 6 a. m. a 6 p. m., hora local. Indiana tiene dos zonas horarias."),
        'id': ("Yes. Show a photo ID issued by Indiana or the U.S. government, such as a driver's license or a U.S. passport. It can be current or expired after November 5, 2024. Without one, you vote a provisional ballot and have until noon on Friday, November 13 to follow up with your county election board.",
               'Sí. Muestra una identificación con foto emitida por Indiana o por el gobierno de EE. UU., como una licencia de conducir o un pasaporte de EE. UU. Puede estar vigente o haber vencido después del 5 de noviembre de 2024. Si no tienes una, votas con una boleta provisional y tienes hasta el mediodía del viernes 13 de noviembre para completar el trámite con la junta electoral de tu condado.'),
        'reg': ('No, not for most voters. The deadline was October 5, 2026, and Indiana has no Election Day registration. Some military voters and their families can register at the county voter registration office through noon on Election Day.',
                'No para la mayoría de los votantes. La fecha límite fue el 5 de octubre de 2026, e Indiana no tiene registro el Día de las Elecciones. Algunos votantes militares y sus familias pueden registrarse en la oficina de registro de votantes del condado hasta el mediodía del Día de las Elecciones.'),
        'selfie': ("Yes, your own ballot. A federal court blocked Indiana's ban on ballot selfies in 2017, and the state didn't appeal. Don't disrupt voting.",
                   'Sí, de tu propia boleta. Un tribunal federal bloqueó en 2017 la prohibición de Indiana de tomarse selfies con la boleta, y el estado no apeló. No interrumpas la votación.'),
    },
    'IA': {
        'polls': ("Polls are open 7 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m."),
        'id': ("You'll be asked for an ID, such as an Iowa driver's license, a U.S. passport, or the free Iowa Voter ID Card. Without one, a registered voter from your precinct can sign an oath vouching for you, and you still vote a regular ballot.",
               'Te pedirán una identificación, como una licencia de conducir de Iowa, un pasaporte de EE. UU. o la credencial gratuita Iowa Voter ID Card. Si no tienes una, un votante registrado de tu precinto puede firmar un juramento que dé fe de quién eres, y aun así votas con una boleta regular.'),
        'reg': ("Yes. Preregistration closed October 19, 2026. You can still register and vote at your polling place on Election Day with ID and proof of where you live.",
                "Sí. El registro previo cerró el 19 de octubre de 2026. Todavía puedes registrarte y votar en tu lugar de votación el Día de las Elecciones con una identificación y un comprobante de domicilio."),
        'selfie': ("Yes, with limits. You can photograph your own voted ballot, but only you and your ballot can be in the photo, and it can't get in other voters' way or disrupt the polling place.",
                   'Sí, con límites. Puedes tomarle foto a tu propia boleta votada, pero en la foto solo pueden salir tú y tu boleta, y no puede estorbar a otros votantes ni interrumpir el lugar de votación.'),
    },
    'KS': {
        'polls': ('Polls are open 7 a.m. to 7 p.m. Central Time unless your county sets other hours, from as early as 6 a.m. to as late as 8 p.m. Counties on Mountain Time are open 6 a.m. to 6 p.m. Mountain Time unless the county sets other hours.',
                  'Los lugares de votación abren de 7 a. m. a 7 p. m., hora del Centro, salvo que tu condado fije otro horario, desde las 6 a. m. hasta las 8 p. m. Los condados con hora de la Montaña abren de 6 a. m. a 6 p. m., hora de la Montaña, salvo que el condado fije otro horario.'),
        'id': ("Most voters do. Show a photo ID, such as a driver's license or a U.S. passport. Without one, you vote a provisional ballot that counts once you get your ID to the county election office before the county canvass. Permanent advance voters, military and overseas voters, and voters who sign a religious objection form are exempt.",
               'La mayoría sí. Muestra una identificación con foto, como una licencia de conducir o un pasaporte de EE. UU. Si no tienes una, votas con una boleta provisional que cuenta cuando llevas tu identificación a la oficina electoral del condado antes del escrutinio del condado (county canvass). Los votantes anticipados permanentes, los votantes militares y en el extranjero, y quienes firman un formulario de objeción religiosa están exentos.'),
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
        'id': ("You'll be asked for a photo ID with your name and signature, such as a Louisiana driver's license, LA Wallet or a Louisiana special ID card, or a U.S. military ID with your name and picture. Without one, you can still vote: answer questions to confirm who you are and sign a voter affidavit.",
               'Te pedirán una identificación con foto, tu nombre y tu firma, como una licencia de conducir de Louisiana, LA Wallet o una tarjeta de identificación especial de Louisiana, o una identificación militar de EE. UU. con tu nombre y foto. Si no tienes una, todavía puedes votar: contesta las preguntas que confirman quién eres y firma una declaración jurada de votante.'),
        'reg': ("No, not for the November 3 election. The deadlines were October 5 in person or by mail and October 13, 2026 online, and Louisiana has no Election Day registration.",
                "No para la elección del 3 de noviembre. Las fechas límite fueron el 5 de octubre en persona o por correo y el 13 de octubre de 2026 en línea, y Louisiana no tiene registro el Día de las Elecciones."),
        'selfie': ("Don't share one. Louisiana law bars voters from allowing a ballot to be seen, except as the law provides. Share a sticker instead.",
                   'No la compartas. La ley de Louisiana prohíbe a los votantes dejar que se vea una boleta, salvo en los casos que permite la ley. Mejor comparte un sticker.'),
    },
    'ME': {
        'polls': ("Polls close at 8 p.m. Each town sets its opening time, between 6 a.m. and 8 a.m., or as late as 10 a.m. in towns with fewer than 500 people.",
                  "Los lugares de votación cierran a las 8 p. m. Cada municipio fija su hora de apertura, entre las 6 a. m. y las 8 a. m., o hasta las 10 a. m. en municipios de menos de 500 habitantes."),
        'id': ("No, if you're already registered. You state your name and address to the election clerk. If you register in person, you must show proof of identity and residence. Without it, you can still vote a challenged ballot.",
               'No, si ya estás registrado. Le dices tu nombre y tu dirección al empleado electoral. Si te registras en persona, debes mostrar un comprobante de identidad y de domicilio. Si no lo tienes, igual puedes votar con una boleta impugnada (challenged ballot).'),
        'reg': ("Yes. Mail and online registration closed October 13, 2026, but you can register in person with your town's registrar through Election Day, including at the polls.",
                "Sí. El registro por correo y en línea cerró el 13 de octubre de 2026, pero puedes registrarte en persona con el registrador de tu municipio hasta el Día de las Elecciones, incluso en el lugar de votación."),
        'selfie': ("Yes. Maine's Secretary of State says ballot selfies are allowed. Other photos or video of ballots are not, and anyone filming in the polling place must stay outside the guardrail and 15 feet from voters.",
                   'Sí. La Secretaría de Estado de Maine dice que se permiten las selfies con tu boleta. Otras fotos o videos de boletas no, y quien grabe en el lugar de votación debe quedarse fuera de la barandilla y a 15 pies de los votantes.'),
    },
    'MD': {
        'polls': ("Polls are open 7 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m."),
        'id': ("Usually not. Some first-time voters are asked for ID, such as a Maryland driver's license, a student ID, or a utility bill dated within 3 months.",
               'Por lo general, no. A algunos votantes nuevos se les pide una identificación, como una licencia de conducir de Maryland, una identificación de estudiante o un recibo de servicios con fecha de los últimos 3 meses.'),
        'reg': ("Yes. The advance deadline was October 13, 2026, but you can register and vote during early voting or at your polling place on Election Day. Bring a document that shows where you live.",
                "Sí. La fecha límite anticipada fue el 13 de octubre de 2026, pero puedes registrarte y votar durante la votación anticipada o en tu lugar de votación el Día de las Elecciones. Lleva un documento que muestre tu domicilio."),
        'selfie': ('Yes, for personal use. You can photograph your own marked ballot before you cast it. Inside, you can otherwise use your phone only to translate, show proof of where you live or ID, or look up election information.',
                   'Sí, para uso personal. Puedes tomarle foto a tu propia boleta marcada antes de depositarla. Fuera de eso, adentro solo puedes usar tu celular para traducir, mostrar tu comprobante de domicilio o tu identificación, o revisar información electoral.'),
    },
    'MA': {
        'polls': ("Polls close at 8 p.m. They open by 7 a.m., and some cities and towns open as early as 5:45 a.m.",
                  "Los lugares de votación cierran a las 8 p. m. Abren a más tardar a las 7 a. m., y algunas ciudades y municipios abren desde las 5:45 a. m."),
        'id': ("Usually not. You may need ID if you're voting for the first time in Massachusetts, are on the inactive list, are casting a provisional or challenged ballot, or a poll worker has a practical and legal reason to ask. It needs your name and registered address. A photo isn't required.",
               'Por lo general, no. Puede que necesites identificación si votas por primera vez en Massachusetts, estás en la lista de votantes inactivos, votas con una boleta provisional o impugnada, o un trabajador electoral tiene una razón práctica y legal para pedirla. Debe tener tu nombre y la dirección donde estás registrado. No tiene que tener foto.'),
        'reg': ('Only if you became a U.S. citizen after the deadline. Registration closed October 24, 2026: 11:59 p.m. online, 5 p.m. in person, or a mailed form postmarked that day. New citizens can register in person at their local election office until 4 p.m. on Monday, November 2, with proof of their naturalization date.',
                'Solo si obtuviste la ciudadanía de EE. UU. después de la fecha límite. El registro cerró el 24 de octubre de 2026: a las 11:59 p. m. en línea, a las 5 p. m. en persona, o con un formulario enviado por correo con matasellos de ese día. Quienes acaban de obtener la ciudadanía pueden registrarse en persona en su oficina electoral local hasta las 4 p. m. del lunes 2 de noviembre, con un comprobante de la fecha de su naturalización.',
                {'until': '2026-11-02', 'en': 'No, not for the November 3 election. Registration closed October 24, 2026, and the extra window for new U.S. citizens ended at 4 p.m. on Monday, November 2.',
                 'es': 'No, no para la elección del 3 de noviembre. El registro cerró el 24 de octubre de 2026, y el plazo extra para quienes acaban de obtener la ciudadanía de EE. UU. terminó a las 4 p. m. del lunes 2 de noviembre.'}),
        'selfie': ("No. Massachusetts law makes it a crime to let anyone see how you marked your ballot for a purpose the law doesn't allow, and a photo you share would show it.",
                   "No. En Massachusetts es un delito dejar que alguien vea cómo marcaste tu boleta con un fin que la ley no permite, y una foto que compartas lo mostraría."),
    },
    'MI': {
        'polls': ("Polls are open 7 a.m. to 8 p.m. local time.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m., hora local."),
        'id': ("If you have a photo ID, such as a driver's license, a U.S. passport, or a student ID, you'll be asked to show it. If you don't have one or forgot it, you can sign an affidavit and vote a regular ballot.",
               'Si tienes una identificación con foto, como una licencia de conducir, un pasaporte de EE. UU. o una identificación de estudiante, te pedirán que la muestres. Si no tienes una o se te olvidó, puedes firmar una declaración jurada y votar con una boleta regular.'),
        'reg': ("Yes. Online and mail registration closed October 19, 2026. You can still register in person at your city or township clerk's office through 8 p.m. on Election Day, with proof of residency.",
                "Sí. El registro en línea y por correo cerró el 19 de octubre de 2026. Todavía puedes registrarte en persona en la oficina del secretario de tu ciudad o municipio (clerk) hasta las 8 p. m. del Día de las Elecciones, con un comprobante de domicilio."),
        'selfie': ("Yes, in the voting booth. You can take a picture of your own ballot there, but you can't hold it up for a selfie or share it within 100 feet of the polling place. Take selfies outside.",
                   'Sí, en la cabina de votación. Ahí puedes tomarle una foto a tu propia boleta, pero no puedes levantarla para una selfie ni compartirla a menos de 100 pies del lugar de votación. Tómate selfies afuera.'),
    },
    'MN': {
        'polls': ("Polls are open 7 a.m. to 8 p.m. Some small towns open later, but no later than 10 a.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m. Algunos pueblos pequeños abren más tarde, pero no después de las 10 a. m."),
        'id': ("No, if you're already registered; you sign the polling place roster. If you register at the polls, you'll need proof of where you live.",
               "No, si ya estás registrado; firmas la lista de votantes del lugar de votación. Si te registras ahí, necesitarás un comprobante de domicilio."),
        'reg': ('Yes. Preregistration closed October 13, 2026, but you can register when you vote early in person through November 2 or at your polling place on Election Day. Bring proof of where you live.',
                'Sí. El registro previo cerró el 13 de octubre de 2026, pero puedes registrarte cuando votes por anticipado en persona hasta el 2 de noviembre o en tu lugar de votación el Día de las Elecciones. Lleva un comprobante de domicilio.',
                {'until': '2026-11-02', 'en': 'Yes, at your polling place on Election Day. Preregistration closed October 13, 2026. Bring proof of where you live.',
                 'es': 'Sí, en tu lugar de votación el Día de las Elecciones. El registro previo cerró el 13 de octubre de 2026. Lleva un comprobante de domicilio.'}),
        'selfie': ('Better not. No Minnesota law strictly bans photos of your own voting, but the Secretary of State discourages them, and a photo of your marked ballot could break the law against showing it to others. Take your sticker selfie outside.',
                   'Mejor no. Ninguna ley de Minnesota prohíbe del todo las fotos de tu propio voto, pero la Secretaría de Estado las desaconseja, y una foto de tu boleta marcada podría violar la ley que prohíbe mostrarla a otras personas. Tómate la selfie con tu sticker afuera.'),
    },
    'MS': {
        'polls': ("Polls are open 7 a.m. to 7 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m."),
        'id': ("Yes. Show a photo ID, such as a Mississippi driver's license or a U.S. passport. Without one, you vote an affidavit ballot and have five business days to show ID.",
               "Sí. Muestra una identificación con foto, como una licencia de conducir de Mississippi o un pasaporte de EE. UU. Si no tienes una, votas con una boleta de declaración jurada (affidavit ballot) y tienes cinco días hábiles para mostrar tu identificación."),
        'reg': no_reg('Mississippi', 'October 5, 2026', '5 de octubre de 2026'),
        'selfie': ("No. Mississippi's Secretary of State says voters can't take pictures of their marked ballot. Take your sticker selfie after you leave.",
                   'No. La Secretaría de Estado de Mississippi dice que los votantes no pueden tomarle fotos a su boleta marcada. Tómate la selfie con tu sticker cuando salgas.'),
    },
    'MO': {
        'polls': ("Polls are open 6 a.m. to 7 p.m. If you're in line at 7 p.m., you can still vote.",
                  "Los lugares de votación abren de 6 a. m. a 7 p. m. Si estás en la fila a las 7 p. m., todavía puedes votar."),
        'id': ("Yes. Show a photo ID, such as a Missouri driver's license or a U.S. passport. Without one, you can vote a provisional ballot, on Election Day or when you vote absentee in person. It counts if you bring an accepted ID back by 7 p.m. on Election Day or your election office matches your signature.",
               'Sí. Muestra una identificación con foto, como una licencia de conducir de Missouri o un pasaporte de EE. UU. Si no tienes una, puedes votar con una boleta provisional, el Día de las Elecciones o cuando votas en ausencia en persona. Cuenta si regresas con una identificación aceptada a más tardar a las 7 p. m. del Día de las Elecciones o si tu oficina electoral confirma que tu firma coincide.'),
        'reg': ('No, not for the November 3 election. The deadline was October 7, 2026, and Missouri has no Election Day registration. Military and overseas voters who left the service, came home or left a job abroad after that date can register in person until 5 p.m. Friday, October 30.',
                'No para la elección del 3 de noviembre. La fecha límite fue el 7 de octubre de 2026, y Missouri no tiene registro el Día de las Elecciones. Los votantes militares y en el extranjero que dejaron el servicio, regresaron o dejaron un trabajo fuera del país después de esa fecha pueden registrarse en persona hasta las 5 p. m. del viernes 30 de octubre.',
                {'until': '2026-10-30', 'en': 'No, not for the November 3 election. The deadline was October 7, 2026, and Missouri has no Election Day registration.',
                 'es': 'No, no para la elección del 3 de noviembre. La fecha límite fue el 7 de octubre de 2026, y Missouri no tiene registro el Día de las Elecciones.'}),
        'selfie': ("Don't share a photo of your marked ballot. Missouri law makes it an offense to let anyone see your ballot so they know how you voted. No state rule we found covers other photos at the polls, so ask a poll worker.",
                   'No compartas una foto de tu boleta marcada. En Missouri es delito dejar que alguien vea tu boleta para que sepa cómo votaste. No encontramos ninguna regla estatal sobre otras fotos en el lugar de votación, así que pregunta a un trabajador electoral.'),
    },
    'OR': {
        'mail': ('Oregon votes by mail. Since October 28, the Secretary of State recommends returning your ballot at an official drop box or your county elections office by 8 p.m. on Election Day. A mailed ballot must be postmarked by Election Day and arrive by November 10.',
                 'Oregon vota por correo. Desde el 28 de octubre, la Secretaría de Estado recomienda devolver tu boleta en un buzón de entrega oficial o en la oficina electoral de tu condado a más tardar a las 8 p. m. del Día de las Elecciones. Una boleta por correo debe tener matasellos del Día de las Elecciones o antes y llegar a más tardar el 10 de noviembre.'),
        'id': ("No. Oregon votes by mail, and your county checks the signature on your ballot envelope against your registration.",
               "No. Oregon vota por correo, y tu condado compara la firma del sobre de tu boleta con la de tu registro."),
        'reg': no_reg('Oregon', 'October 13, 2026', '13 de octubre de 2026'),
    },
    'PA': {
        'polls': ("Polls are open 7 a.m. to 8 p.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m."),
        'id': ("Usually not. If it's your first time voting at your polling place, bring an ID, such as a driver's license or a utility bill.",
               "Por lo general, no. Si es la primera vez que votas en tu lugar de votación, lleva una identificación, como una licencia de conducir o un recibo de servicios."),
        'reg': ("No, not for the November 3 election. The deadline was October 19, 2026, and Pennsylvania has no Election Day registration.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 19 de octubre de 2026, y Pennsylvania no tiene registro el Día de las Elecciones."),
        'selfie': ("Yes. Pennsylvania's Attorney General says you can take a ballot selfie. Keep other voters' ballots out of the photo, and wait until you've left the polling place to post it.",
                   'Sí. La Fiscalía General (Attorney General) de Pennsylvania dice que puedes tomarte una selfie con tu boleta. No incluyas las boletas de otros votantes en la foto, y espera a salir del lugar de votación para publicarla.'),
    },
    'RI': {
        'polls': ("Polls are open 7 a.m. to 8 p.m. In New Shoreham, on Block Island, they open at 9 a.m.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m. En New Shoreham, en Block Island, abren a las 9 a. m."),
        'id': ("Bring a photo ID, such as a Rhode Island driver's license, a free voter ID card, or a U.S. passport. Without one, you vote an emergency mail ballot during early voting, or a provisional ballot on Election Day that counts if your signature matches.",
               "Lleva una identificación con foto, como una licencia de conducir de Rhode Island, una credencial gratuita de votante o un pasaporte de EE. UU. Si no tienes una, votas con una boleta de emergencia por correo durante la votación anticipada, o con una boleta provisional el Día de las Elecciones que cuenta si tu firma coincide."),
        'reg': ("No, not for the November 3 election. The deadline was October 4, 2026. Rhode Island allows Election Day registration only in presidential elections, and then only to vote for president and vice president.",
                "No para la elección del 3 de noviembre. La fecha límite fue el 4 de octubre de 2026. Rhode Island permite registrarse el Día de las Elecciones solo en elecciones presidenciales, y solo para votar por presidente y vicepresidente."),
        'selfie': ("Yes. The Board of Elections' poll worker manual says photos of your own ballot are allowed. Phone calls inside the polling place aren't, unless they're about the election.",
                   "Sí. El manual para trabajadores electorales de la Junta Electoral (Board of Elections) dice que se permiten fotos de tu propia boleta. Las llamadas dentro del lugar de votación no se permiten, salvo que sean sobre la elección."),
    },
    'SC': {
        'polls': ("Polling places are open 7 a.m. to 7 p.m. If you're in line at 7 p.m., you can vote.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. Si estás en la fila a las 7 p. m., puedes votar."),
        'id': ("You'll be asked for a photo ID, such as a South Carolina driver's license or a U.S. passport. Forgot it? Vote a provisional ballot and show your ID at the county office before the election is certified. Can't get one? Bring your voter registration card, sign an affidavit, and vote a provisional ballot.",
               'Te pedirán una identificación con foto, como una licencia de conducir de South Carolina o un pasaporte de EE. UU. ¿La olvidaste? Vota con una boleta provisional y muestra tu identificación en la oficina del condado antes de que se certifique la elección. ¿No puedes conseguir una? Lleva tu tarjeta de registro de votante, firma una declaración jurada y vota con una boleta provisional.'),
        'reg': ("No, not for the November 3 election. Online registration closed October 4, 2026, and South Carolina has no same-day registration.",
                "No para la elección del 3 de noviembre. El registro en línea cerró el 4 de octubre de 2026, y South Carolina no tiene registro el mismo día."),
        'selfie': ("No. Taking pictures or recordings inside a polling place isn't allowed, during early voting or on Election Day, and state law bars letting anyone see your ballot.",
                   'No. No se permite tomar fotos ni grabar dentro de un lugar de votación, ni en la votación anticipada ni el Día de las Elecciones, y la ley estatal prohíbe dejar que alguien vea tu boleta.'),
    },
    'SD': {
        'polls': ("Polls are open 7 a.m. to 7 p.m. local time. South Dakota has two time zones.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m., hora local. South Dakota tiene dos zonas horarias."),
        'id': ("You'll be asked for a photo ID, such as a South Dakota driver's license. Without one, you can sign an affidavit and still vote a regular ballot.",
               "Te pedirán una identificación con foto, como una licencia de conducir de South Dakota. Si no tienes una, puedes firmar una declaración jurada y votar con una boleta regular."),
        'reg': ("No, not for the November 3 election. Your signed form had to reach your county auditor by October 19, 2026, and South Dakota has no Election Day registration.",
                "No para la elección del 3 de noviembre. Tu formulario firmado tenía que llegar al auditor de tu condado (county auditor) a más tardar el 19 de octubre de 2026, y South Dakota no tiene registro el Día de las Elecciones."),
        'selfie': ("Don't share one. South Dakota law bars showing anyone your marked ballot in a way that reveals your votes.",
                   "No la compartas. La ley de South Dakota prohíbe mostrarle a cualquier persona tu boleta marcada de una forma que revele tus votos."),
    },
    'TN': {
        'polls': ('Opening times vary by county. Polls close at 8 p.m. in Eastern Time counties and 7 p.m. in Central Time counties.',
                  'La hora de apertura cambia según el condado. Los lugares de votación cierran a las 8 p. m. en los condados con hora del Este y a las 7 p. m. en los condados con hora del Centro.'),
        'id': ("In most cases, yes. Show a federal or Tennessee photo ID, such as a Tennessee driver's license, a U.S. passport, or a military ID. It can be expired. Without one, you vote a provisional ballot and have until Thursday, November 5 to show ID at your county election commission office.",
               'En la mayoría de los casos, sí. Muestra una identificación con foto federal o de Tennessee, como una licencia de conducir de Tennessee, un pasaporte de EE. UU. o una identificación militar. Puede estar vencida. Si no tienes una, votas con una boleta provisional y tienes hasta el jueves 5 de noviembre para mostrar tu identificación en la oficina de la comisión electoral de tu condado.'),
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
        'selfie': ('Not at the polls. Texas law bars recording images or sound within 100 feet of a voting station, so take your sticker photo well away from it.',
                   'No en el lugar de votación. La ley de Texas prohíbe grabar imágenes o sonido a menos de 100 pies de una cabina de votación, así que tómate la foto con tu sticker lejos de ahí.'),
    },
    'UT': {
        'polls': ("Polling places are open 7 a.m. to 8 p.m. Utah also mails ballots to voters. Your mail ballot must reach the county clerk by 8 p.m. on November 3, and a postmark isn't enough.",
                  "Los lugares de votación abren de 7 a. m. a 8 p. m. Utah también envía boletas por correo a los votantes. Tu boleta por correo debe llegar a la oficina del secretario del condado (county clerk) a más tardar a las 8 p. m. del 3 de noviembre, y el matasellos no basta."),
        'id': ("Yes, to vote in person. Show one photo ID, such as a Utah driver's license or a U.S. passport, or two documents that together prove your name and address.",
               "Sí, para votar en persona. Muestra una identificación con foto, como una licencia de conducir de Utah o un pasaporte de EE. UU., o dos documentos que juntos comprueben tu nombre y tu dirección."),
        'reg': ('Yes. The regular deadline, for registrations to reach your county clerk, was 5 p.m. on October 23, 2026. You can still register in person during early voting or on Election Day with two forms of ID. One ID must prove who you are and one where you live.',
                'Sí. La fecha límite regular para que tu registro llegara a la oficina del secretario de tu condado fue a las 5 p. m. del 23 de octubre de 2026. Todavía puedes registrarte en persona durante la votación anticipada o el Día de las Elecciones con dos formas de identificación. Una identificación debe comprobar quién eres y otra dónde vives.'),
        'selfie': ("Yes. Utah law lets you photograph your own ballot and share it, whether you vote by mail or in person. Photographing someone else's ballot at a polling place is a misdemeanor.",
                   'Sí. La ley de Utah te permite tomarle foto a tu propia boleta y compartirla, ya sea que votes por correo o en persona. Tomarle foto a la boleta de otra persona en un lugar de votación es un delito menor.'),
    },
    'VT': {
        'polls': ("Polls open between 5 a.m. and 10 a.m., depending on the town, and close at 7 p.m. Vermont also mails a ballot to every active voter. Return it to your town clerk or drop box by the day before the election, mail it so the clerk gets it before the polls close, or bring it to your polling place by 7 p.m.",
                  "Los lugares de votación abren entre las 5 a. m. y las 10 a. m., según el municipio, y cierran a las 7 p. m. Vermont también envía una boleta por correo a cada votante activo. Devuélvela al secretario de tu municipio (town clerk) o al buzón de entrega a más tardar el día antes de la elección, mándala por correo para que el secretario la reciba antes de que cierren los lugares de votación, o llévala a tu lugar de votación antes de las 7 p. m."),
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
        'reg': ("Yes, in person. The regular deadline was October 23, 2026. During early voting, register at your general registrar's office or a satellite site; on Election Day, go to the polling place for the precinct where you live. You vote a provisional ballot.",
                'Sí, en persona. La fecha límite regular fue el 23 de octubre de 2026. Durante la votación anticipada, regístrate en la oficina de registro de tu localidad (general registrar) o en un lugar satélite; el Día de las Elecciones, ve al lugar de votación del precinto donde vives. Votas con una boleta provisional.'),
        'selfie': ("Yes, at the polls. A 2016 Attorney General opinion says state law doesn't ban photographing your own ballot. You can't use your phone to get voting help from someone outside the polling place.",
                   'Sí, en el lugar de votación. Una opinión de 2016 de la Fiscalía General (Attorney General) dice que la ley estatal no prohíbe tomarle foto a tu propia boleta. No puedes usar tu teléfono para que alguien de afuera te ayude a votar.'),
    },
    'WA': {
        'mail': ('Washington votes by mail. Put your ballot in an official drop box by 8 p.m. on Election Day, or mail it with a postmark dated Election Day or earlier. In the last week, use a drop box, or ask a post office counter to hand-stamp the postmark.',
                 'Washington vota por correo. Deja tu boleta en un buzón de entrega oficial a más tardar a las 8 p. m. del Día de las Elecciones, o envíala con matasellos del Día de las Elecciones o antes. En la última semana, usa un buzón de entrega o pide en el mostrador de la oficina postal que le pongan el matasellos a mano.'),
        'id': ("Not to vote by mail. Your county checks the signature on your ballot envelope. If you vote in person at a voting center, you sign a ballot declaration or show a photo ID, such as a driver's license, state ID, student ID or tribal ID.",
               'No para votar por correo. Tu condado revisa la firma del sobre de tu boleta. Si votas en persona en un centro de votación, firmas una declaración en la boleta o muestras una identificación con foto, como una licencia de conducir, una identificación estatal, una identificación de estudiante o una identificación tribal.'),
        'reg': ("Yes. Online and mail registrations must arrive by October 26, 2026, and you can register in person until 8 p.m. on Election Day.",
                "Sí. Los registros en línea y por correo deben llegar a más tardar el 26 de octubre de 2026, y puedes registrarte en persona hasta las 8 p. m. del Día de las Elecciones.",
                # After the date passes (October 25): what the page says instead.
                {'until': '2026-10-26', 'en': "Yes, in person. Online and mail registrations had to arrive by October 26, 2026. You can still register in person until 8 p.m. on Election Day.",
                 'es': "Sí, en persona. Los registros en línea y por correo tenían que llegar a más tardar el 26 de octubre de 2026. Todavía puedes registrarte en persona hasta las 8 p. m. del Día de las Elecciones."}),
        'selfie': ("Yes. Washington doesn't prohibit ballot selfies. It's illegal to view someone else's ballot for a purpose such as vote buying.",
                   "Sí. Washington no prohíbe las selfies con la boleta. Es ilegal ver la boleta de otra persona con un fin como comprar votos."),
    },
    'WV': {
        'polls': ("Polls are open 6:30 a.m. to 7:30 p.m. at your assigned polling place.",
                  "Los lugares de votación abren de 6:30 a. m. a 7:30 p. m. en el lugar que te corresponde."),
        'id': ("Yes. Show an unexpired photo ID, such as a West Virginia driver's license, a U.S. passport, or a West Virginia student ID. Without one, an adult or poll worker who has known you for six months can vouch for you; otherwise you vote a provisional ballot.",
               "Sí. Muestra una identificación vigente con foto, como una licencia de conducir de West Virginia, un pasaporte de EE. UU. o una identificación de estudiante de West Virginia. Si no tienes una, un adulto o un trabajador electoral que te conozca desde hace seis meses puede dar fe de quién eres; si no, votas con una boleta provisional."),
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
                "Sí. El registro en línea y por correo cerró el 14 de octubre de 2026. Puedes registrarte en la oficina del secretario municipal hasta las 5 p. m. del 30 de octubre, o en tu lugar de votación el Día de las Elecciones. Lleva un comprobante de domicilio.",
                # After the date passes (October 25): what the page says instead.
                {'until': '2026-10-30', 'en': "Yes, at your polling place on Election Day. Online and mail registration closed October 14, 2026, and registration at your clerk's office ended at 5 p.m. on October 30. Bring proof of where you live.",
                 'es': "Sí, en tu lugar de votación el Día de las Elecciones. El registro en línea y por correo cerró el 14 de octubre de 2026, y el registro en la oficina del secretario municipal terminó a las 5 p. m. del 30 de octubre. Lleva un comprobante de domicilio."}),
        'selfie': ('You can take a photo of your own marked ballot for yourself. Posting it, sharing it with anyone or livestreaming your vote is against state law, according to the Elections Commission.',
                   'Puedes tomarle una foto a tu propia boleta marcada para ti. Publicarla, compartirla con alguien o transmitir en vivo tu voto va contra la ley estatal, según la Comisión Electoral.'),
    },
    'WY': {
        'polls': ("Polls are open 7 a.m. to 7 p.m. If you're in line at 7 p.m., you can still vote.",
                  "Los lugares de votación abren de 7 a. m. a 7 p. m. Si estás en la fila a las 7 p. m., todavía puedes votar."),
        'id': ("Yes. Show an acceptable ID, such as a Wyoming driver's license or ID card, a U.S. passport, a tribal ID, or a Medicare or Medicaid card. No acceptable ID with you? You can still vote a provisional ballot after signing an oath. It counts only if you bring proof to your county clerk by close of business Wednesday, November 4.",
               "Sí. Muestra una identificación aceptada, como una licencia de conducir o tarjeta de identificación de Wyoming, un pasaporte de EE. UU., una identificación tribal o una tarjeta de Medicare o Medicaid. ¿No traes una identificación aceptada? Aun así puedes votar con una boleta provisional después de firmar un juramento. Solo se cuenta si llevas un comprobante al secretario de tu condado (county clerk) antes del cierre de oficina del miércoles 4 de noviembre."),
        'reg': ("Yes. Regular registration closed October 19, 2026, but you can register and vote at the same time during early absentee voting or on Election Day. Bring proof of identity, citizenship, and residence.",
                "Sí. El registro regular cerró el 19 de octubre de 2026, pero puedes registrarte y votar al mismo tiempo durante la votación anticipada en ausencia o el Día de las Elecciones. Lleva comprobantes de identidad, ciudadanía y domicilio."),
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
    if not research:
        # The raw research files aren't kept in the repo; docs/faq-sources.json holds the same source
        # fields per state (written by the last full run).
        research = json.loads((ROOT / 'docs' / 'faq-sources.json').read_text())
    lines = ['<?php', '// Generated by build/faq_text.py from verified research (docs/faq-sources.json). Edit faq_text.py, not this file.', 'return array(']
    sources = {}
    for abbr, qs in A.items():
        r = research[abbr]
        lines.append(f"\t'{abbr}' => array(")
        for key, (en, es, *after) in qs.items():
            src = r[KEYMAP[key]]['source']
            # An optional third item: {'until': date, 'en', 'es'}, the answer once that date has passed.
            aft = after[0] if after else {}
            assert '—' not in en + es + aft.get('en', '') + aft.get('es', '') and '–' not in en + es + aft.get('en', '') + aft.get('es', ''), (abbr, key)
            extra = f", 'until' => {php(aft['until'])}, 'en_after' => {php(aft['en'])}, 'es_after' => {php(aft['es'])}" if aft else ''
            if aft.get('src'):  # the after-text's own official page (needs a quote beside it in this file)
                assert aft.get('quote'), (abbr, key, 'src without quote')
                extra += f", 'src_after' => {php(aft['src'])}"
            lines.append(f"\t\t'{key}' => array( 'en' => {php(en)}, 'es' => {php(es)}, 'src' => {php(src)}{extra} ),")
        lines.append('\t),')
        sources[abbr] = {k: r.get(k) for k in ('polls', 'id', 'registration', 'selfie', 'notes')}
    lines.append(');')
    (ROOT / 'site' / 'faq-states.php').write_text('\n'.join(lines) + '\n')
    (ROOT / 'docs' / 'faq-sources.json').write_text(json.dumps(sources, indent=1, ensure_ascii=False) + '\n')
    print(f'{len(A)} states, {sum(len(q) for q in A.values())} answers')


if __name__ == '__main__':
    import sys
    main(sys.argv[1] if len(sys.argv) > 1 else str(ROOT / 'build' / 'faq-research'))
