var Consultations = React.createClass({
   loadClientsFromServer: function() {
     $.ajax({
         url: this.props.getUrl,
         dataType: 'json',
         success: function(data) {
           this.setState({
              clients: data, 
              currentClient : (this.state.currentClient || this.props.id ? 
              jQuery.grep(data, function(client, i) {
                  return (client.id === (this.state.currentClient ? this.state.currentClient.id : parseInt(this.props.id)));
              }.bind(this))[0] : null)
           });
         }.bind(this),
         error: function(xhr, status, err) {
           console.error(this.props.url, status, err.toString());
         }.bind(this)
      }); 
   },
   getInitialState: function() {
      return {clients: [], currentClient : null};
   },
   componentDidMount: function() {
      this.loadClientsFromServer();
      setInterval(this.loadClientsFromServer, this.props.pollInterval);
   },
   handleSelectClient: function(client) {
      this.setState({currentClient:client}); 
   },
   handleMessageSubmit: function(message) {
      var currentClient = this.state.currentClient;
      var today = new Date();
      var dd = today.getDate();
      var mm = today.getMonth() + 1; //January is 0!
      var yyyy = today.getFullYear();
      var hh = today.getHours() + 1;
      var ii = today.getMinutes();
      if(dd < 10){
          dd = '0' + dd;
      } 
      if(mm<10){
          mm = '0' + mm;
      }
      if(hh<10){
          hh = '0' + hh;
      }
      if(ii < 10){
          ii = '0' + ii;
      }
      today = dd+'.'+mm+'.'+yyyy + ' ' + hh + ':' + ii;
      currentClient.messages = currentClient.messages.concat([{message: message, isToClient : true, created: today}]);;
      this.setState({currentClient : currentClient});
      $.ajax({
         url: this.props.sendUrl,
         dataType: 'json',
         type: 'POST',
         data: {message: message, client: this.state.currentClient.id},
         success: function(data) {
            this.setState({currentClient: data});
         }.bind(this),
         error: function(xhr, status, err) {
           console.error(this.props.url, status, err.toString());
         }.bind(this)
      });
   },
   render: function() {
      var clientInfo;
      var messageBlock;
      if (this.state.currentClient) {
          messageBlock = (
            <div>
               <Messages client={this.state.currentClient} />
               <MessageForm onMessageSubmit={this.handleMessageSubmit} />
            </div>
          );
          clientInfo = <ClientInfo client={this.state.currentClient} />;
      } else {
          messageBlock = <EmptyMessage />;
      }
      return (
         <div className="consultations row">
            <div className="col-lg-10">
               {clientInfo}
               {messageBlock}
            </div>
            <div className="col-lg-2">
               <Clients onSelectClient={this.handleSelectClient} clients={this.state.clients} currentClient={this.state.currentClient} />
            </div>
         </div>
      );
   }
});

var ClientInfo = React.createClass({
   render: function() {
      return (
         <div className="clientInfo">
            <p>Клиент #{this.props.client.id}</p>
            <p>{this.props.client.site}</p>
         </div>
      );
   }
});

var Messages = React.createClass({
   render: function() {
      var messagesNodes = this.props.client.messages.map(function(message) {
         return (
           <Message message={message}></Message>
         );
      });
      return (
         <div className="list-group">
         {messagesNodes}
         </div>
      );
   }
});

var MessageForm = React.createClass({
   handleSubmit: function(e) {
      e.preventDefault();
      var text = this.refs.text.getDOMNode().value.trim();
      if (!text) {
        return;
      }
      this.props.onMessageSubmit(text);
      this.refs.text.getDOMNode().value = '';
   },
   render: function() {
      return (
        <form className="messageForm form-group" onSubmit={this.handleSubmit}>
         <textarea type="text" ref="text" className="form-control" placeholder="Ваше сообщение..." />
         <input type="submit" className="btn btn-primary" value="Отправить" />
        </form>
      );
   }
});

var Message = React.createClass({
   render: function() {
      var cx = React.addons.classSet;
      var classes = cx({
        'list-group-item': true,
        'list-group-item-success': this.props.message.isToClient,
        'list-group-item-danger': !this.props.message.isToClient
      });
      return (
         <li className={classes}>{this.props.message.message}<span className="badge">{this.props.message.created}</span></li>
      );
   }
});

var EmptyMessage = React.createClass({
   render: function() {
      return (
         <div className="emptyMessage alert alert-info">Выберете собеседника в списке справа!</div>
      );
   }
});

var Clients = React.createClass({
   handleSelectClient: function(client) {
      this.props.onSelectClient(client); 
   },
   render: function() {
      var clientNodes = this.props.clients.map(function(client) {
         return (
           <Client client={client} onSelectClient={this.handleSelectClient} currentClient={this.props.currentClient} />
         );
      }.bind(this));
      return (
         <div className="clients">
            <div className="list-group">
            {clientNodes}
            </div>
         </div>
      );
   }
});

var Client = React.createClass({
   selectClient: function(e) {
      e.preventDefault();
      this.props.onSelectClient(this.props.client);
   },
   render: function() {
      var cx = React.addons.classSet;
      var classes = cx({
        'list-group-item': true,
        'active': this.props.currentClient && this.props.currentClient.id === this.props.client.id
      });
      return (
         <a href="javascript:void(0);" className={classes} onClick={this.selectClient}>Клиент #{this.props.client.id}</a>
      );
   }
});
if (document.getElementById('consultations')) {
   React.render(
           <Consultations
               id={document.getElementById('consultations').getAttribute('rel')}
               sendUrl={document.getElementById('consultations').getAttribute('data-send')} 
               getUrl={document.getElementById('consultations').getAttribute('data-get')} 
               pollInterval={2000} />, 
           document.getElementById('consultations')
   );
}