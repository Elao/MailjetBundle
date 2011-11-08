Configuration

fichier config.yml

    elao_mail_jet:
        api_key:        your api key
        secret_key:     your api secret

Configure your endpoint url in https://www.mailjet.com/account/triggers

Available Events:
    elao.mailjet.trigger: dispatch for all event
    elao.mailjet.trigger.open
    elao.mailjet.trigger.click
    elao.mailjet.trigger.bounce
    elao.mailjet.trigger.spam
    elao.mailjet.trigger.blocked
    elao.mailjet.trigger.tipofix
    elao.mailjet.trigger.unsub