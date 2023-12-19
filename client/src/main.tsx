import ReactDOM from 'react-dom/client'
import App from './App.tsx'
import './index.css'
import ReduxProvider from '@redux/provider'
import { BrowserRouter as Router } from 'react-router-dom'
import { Toaster } from '@/components/ui/toaster.tsx'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { ReactQueryDevtools } from '@tanstack/react-query-devtools'

const queryClient = new QueryClient()
ReactDOM.createRoot(document.getElementById('root')!).render(
  <QueryClientProvider client={queryClient}>
    <ReduxProvider>
      <Router>
        <App />
        <Toaster />
      </Router>
    </ReduxProvider>
    <ReactQueryDevtools initialIsOpen={false} />
  </QueryClientProvider>,
)
