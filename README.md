[![CodeFactor](https://www.codefactor.io/repository/github/pilo1996/fmcrestapi/badge)](https://www.codefactor.io/repository/github/pilo1996/fmcrestapi)

# Find My Coso REST API con Framework Slim 3

API REST create da uno scheletro del framework Slim versione 3 (attualmente 4) per via di compatibilità.
API utilizzabili per l'app Find My Coso SE.

## Lista e descrizione API

### Utenti

- `/createuser`
  Permette la creazione dell'utente, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Utente creato correttamente. |
  | 422 | Sì | Utente già registrato. |
  | 423 | Sì | Utente NON creato. |
  
- `/userLogin`
  Permette il login dell'utente (verifica), ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 200 | No | Utente autenticato correttamente. |
  | 422 | Sì | Password non corretta. |
  | 422 | Sì | Utente NON trovato. |

- `/updateUser/{id}` 
  Aggiorna nome e profile pic dell'utente, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 200 | No | Utente aggiornato. |
  | 422 | Sì | Impossibile aggiornare l'utente. |

- `/updatePassword`
  Permette la modifica della password (funzionalità prevista solo nel sito web), ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Password aggiornata. |
  | 422 | Sì | Password attuale invalida. |
  | 422 | Sì | Impossibile cambiare la password. |

- `/updateSelectedDevice`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |


TODO: api per richiedere reset password

### Posizioni

- `/getallPositions`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/getAllPositionsFromDeviceID`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/addPosition`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/deleteAllPositionsByDevice/{deviceID, userID}`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/deleteSinglePositionByID/{locationID}`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |


### Dispositivi

- `/registerDevice`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/updateDevice` deprecato per limiti di design app
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/removeDeviceRegistered/{deviceID}`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/bookmarkDevice`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/removeBookmarkedDevice/{deviceID, userID}`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/getAllDevicesRegistered/{userID}`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/getAllDevicesBookmarked/{userID}`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |

- `/getAllSavedDevices`
  Per aggiornare la preferenza del dispositivo selezionato da visualizzarne i dati, ritorna:
  | Codice | isError | Messaggio |
  |--------|---------|-----------|
  | 201 | No | Dispositivo selezionato aggiornato. |
  | 422 | Sì | Impossibile selezionare il dispositivo. |


TODO: get primo dispositivo registrato manualmente che trovo in lista

Readme in costruzione
