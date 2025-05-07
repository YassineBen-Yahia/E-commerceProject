<?php
    namespace App\Controller;

    use App\Service\EmailVerificationService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class MailerController extends AbstractController
    {
    public function __construct(private EmailVerificationService $verificationService) {}

    #[Route('/verify/email', name: 'verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    $error = $this->verificationService->verifyEmail($request, $this->getUser());

    if ($error) {
    $this->addFlash('verify_email_error', $error);
    return $this->redirectToRoute('app_register');
    }

    $this->addFlash('success', 'Your email address has been verified.');
    return $this->redirectToRoute('app_index');
    }
    }
